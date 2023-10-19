<?php

namespace App\MessageHandler;

use App\Entity\EntityId\GasStationId;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Repository\GasStationRepository;
use App\Service\GasStationService;
use App\Service\GooglePlaceApiService;
use App\Service\GooglePlaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler()]
final class CreateGooglePlaceTextsearchMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly GasStationRepository $gasStationRepository,
        private readonly GasStationService $gasStationService,
        private readonly GooglePlaceService $googlePlaceService,
        private readonly MessageBusInterface $messageBus,
        private readonly GooglePlaceApiService $googlePlaceApiService
    ) {
    }

    public function __invoke(CreateGooglePlaceTextsearchMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $gasStation = $this->gasStationRepository->findOneBy(['gasStationId' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station doesnt exist (id : %s)', $message->getGasStationId()->getId()));
        }

        if (!in_array($gasStation->getStatus(), [
            GasStationStatusReference::ADDRESS_FORMATED,
            GasStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH,
            GasStationStatusReference::NOT_FOUND_IN_TEXTSEARCH,
            GasStationStatusReference::VALIDATION_REJECTED,
        ])) {
            throw new UnrecoverableMessageHandlingException(sprintf('Wrong status for Gas Station (gasStationId : %s)', $message->getGasStationId()->getId()));
        }

        if (GasStationStatusReference::PLACE_ID_ANOMALY === $gasStation->getStatus()) {
            return true;
        }

        $response = $this->googlePlaceApiService->placeTextsearch($gasStation);

        if (null === $response) {
            return $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::NOT_FOUND_IN_TEXTSEARCH);
        }

        $gasStation->getGooglePlace()->setPlaceId($response);
        $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::FOUND_IN_TEXTSEARCH);

        $gasStationsAnomalies = $this->gasStationRepository->getGasStationGooglePlaceByPlaceId($gasStation);

        if (count($gasStationsAnomalies) > 0) {
            return $this->googlePlaceService->createAnomalies(array_merge($gasStationsAnomalies, [$gasStation]));
        }

        return $this->messageBus->dispatch(
            new CreateGooglePlaceDetailsMessage(
                new GasStationId($gasStation->getGasStationId())
            )
        );
    }
}
