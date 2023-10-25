<?php

namespace App\MessageHandler;

use App\Lists\GasStationStatusReference;
use App\Message\ErrorGeocodingAddressMessage;
use App\Repository\AddressRepository;
use App\Repository\GasStationRepository;
use App\Service\AddressService;
use App\Service\GasStationService;
use App\Service\PositionStackApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler()]
final class ErrorGeocodingAddressMessageHandler
{
    public const CONFIDENCE_ERROR = 0.8;

    public function __construct(
        private EntityManagerInterface $em,
        private readonly AddressRepository $addressRepository,
        private readonly PositionStackApiService $positionStackApiService,
        private readonly MessageBusInterface $messageBus,
        private readonly AddressService $addressService,
        private readonly GasStationService $gasStationService,
        private readonly GasStationRepository $gasStationRepository
    ) {
    }

    public function __invoke(ErrorGeocodingAddressMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $address = $this->addressRepository->findOneBy(['id' => $message->getAddressId()->getId()]);

        if (null === $address) {
            throw new UnrecoverableMessageHandlingException(sprintf('Address is null (id: %s)', $message->getAddressId()->getId()));
        }

        $gasStation = $this->gasStationRepository->findOneBy(['gasStationId' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $data = $this->positionStackApiService->reverse($address);

        if (null === $data) {
            return $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::ADDRESS_ERROR_FORMATED);
        }

        $positionStackApiResult = $address->getPositionStackApiResult();
        $positionStackApiResult['reverse_api'] = $data;
        $address->setPositionStackApiResult($positionStackApiResult);

        $this->em->persist($address);
        $this->em->flush();

        if (!array_key_exists('confidence', $data)) {
            return $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::ADDRESS_ERROR_FORMATED);
        }

        if ($data['confidence'] < GeocodingAddressMessageHandler::CONFIDENCE_ERROR) {
            return $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::ADDRESS_ERROR_FORMATED);
        }

        $this->addressService->hydrate($address, $data);
        $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::ADDRESS_FORMATED);

//        return $this->messageBus->dispatch(new CreateGooglePlaceTextsearchMessage($message->getGasStationId()));
    }
}
