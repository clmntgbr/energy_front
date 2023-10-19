<?php

namespace App\Service;

use App\Repository\GasStationRepository;

class GasStationStatusAnomalyCommandService
{
    public function __construct(
        private readonly GasStationRepository $gasStationRepository,
        private readonly GooglePlaceService $googlePlaceService
    ) {
    }

    public function invoke(): void
    {
        $gasStations = $this->gasStationRepository->findGasStationsByPlaceId();

        foreach ($gasStations as $gasStation) {
            $gasStationsAnomalies = $this->gasStationRepository->getGasStationGooglePlaceByPlaceId($gasStation);
            if (count($gasStationsAnomalies) > 0) {
                $this->googlePlaceService->createAnomalies(array_merge($gasStationsAnomalies, [$gasStation]));
            }
        }
    }
}
