<?php

namespace App\Service;

use App\Entity\EntityId\AddressId;
use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Message\GeocodingAddressMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class GasStationStatusUpdateCommandService
{
    public const MAX_RETRY_POSITION_STACK = 5;
    public const MAX_RETRY_TEXT_SEARCH = 5;
    public const MAX_RETRY_PLACE_DETAILS = 5;

    public function __construct(
        private readonly GasStationRepository $gasStationRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function invoke(): void
    {
        $gasStations = $this->gasStationRepository->findAll();

        foreach ($gasStations as $gasStation) {
            match ($gasStation->getStatus()) {
                GasStationStatusReference::CREATED => $this->created($gasStation),
                GasStationStatusReference::UPDATED_TO_ADDRESS_FORMATED => $this->created($gasStation),
                GasStationStatusReference::ADDRESS_ERROR_FORMATED => $this->created($gasStation),

                GasStationStatusReference::ADDRESS_FORMATED => $this->textSearch($gasStation),
                GasStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH => $this->textSearch($gasStation),
                GasStationStatusReference::NOT_FOUND_IN_TEXTSEARCH => $this->textSearch($gasStation),

                GasStationStatusReference::FOUND_IN_TEXTSEARCH => $this->detailsSearch($gasStation),
                GasStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS => $this->detailsSearch($gasStation),
                GasStationStatusReference::NOT_FOUND_IN_DETAILS => $this->detailsSearch($gasStation),

                default => '',
            };
        }
    }

    private function created(GasStation $gasStation): void
    {
        if ($gasStation->getMaxRetryPositionStack() > self::MAX_RETRY_POSITION_STACK) {
            return;
        }

        $gasStation->addMaxRetryPositionStack();
        $this->em->persist($gasStation);
        $this->em->flush();
        $this->messageBus->dispatch(
            new GeocodingAddressMessage(new AddressId($gasStation->getAddress()->getId()), new GasStationId($gasStation->getGasStationId()))
        );
    }

    private function textSearch(GasStation $gasStation): void
    {
        if ($gasStation->getMaxRetryTextSearch() > self::MAX_RETRY_TEXT_SEARCH) {
            return;
        }

        $gasStation->addMaxRetryTextSearch();
        $this->em->persist($gasStation);
        $this->em->flush();
        $this->messageBus->dispatch(
            new CreateGooglePlaceTextsearchMessage(new GasStationId($gasStation->getGasStationId()))
        );
    }

    private function detailsSearch(GasStation $gasStation): void
    {
        if ($gasStation->getMaxRetryPlaceDetails() > self::MAX_RETRY_PLACE_DETAILS) {
            return;
        }

        $gasStation->addMaxRetryPlaceDetails();
        $this->em->persist($gasStation);
        $this->em->flush();
        $this->messageBus->dispatch(
            new CreateGooglePlaceDetailsMessage(new GasStationId($gasStation->getGasStationId()))
        );
    }
}
