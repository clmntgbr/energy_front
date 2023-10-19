<?php

namespace App\MessageHandler;

use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Repository\GasStationRepository;
use App\Service\GasStationService;
use App\Service\GooglePlaceApiService;
use App\Service\GooglePlaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler()]
final class CreateGooglePlaceDetailsMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly GasStationRepository $gasStationRepository,
        private readonly GooglePlaceApiService $googlePlaceApiService,
        private readonly GooglePlaceService $googlePlaceService,
        private readonly GasStationService $gasStationService
    ) {
    }

    public function __invoke(CreateGooglePlaceDetailsMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $gasStation = $this->gasStationRepository->findOneBy(['gasStationId' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station doesnt exist (id : %s)', $message->getGasStationId()->getId()));
        }

        if (!in_array($gasStation->getStatus(), [
            GasStationStatusReference::FOUND_IN_TEXTSEARCH,
            GasStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS,
            GasStationStatusReference::NOT_FOUND_IN_DETAILS,
            GasStationStatusReference::VALIDATION_REJECTED,
        ])) {
            throw new UnrecoverableMessageHandlingException(sprintf('Wrong status for Gas Station (gasStationId : %s)', $message->getGasStationId()->getId()));
        }

        if (GasStationStatusReference::PLACE_ID_ANOMALY === $gasStation->getStatus()) {
            return true;
        }

        $response = $this->googlePlaceApiService->placeDetails($gasStation);

        if (null === $response) {
            return $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::NOT_FOUND_IN_DETAILS);
        }

        $gasStation->setName(htmlspecialchars_decode(ucwords(strtolower(trim($response['name'] ?? null)))));
        $this->googlePlaceService->updateGasStationGooglePlace($gasStation, $response);
        $this->googlePlaceService->updateGasStationAddress($gasStation, $response);

        $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::FOUND_IN_DETAILS);

        return $this->gasStationService->setGasStationStatus($gasStation, GasStationStatusReference::WAITING_VALIDATION);
    }
}
