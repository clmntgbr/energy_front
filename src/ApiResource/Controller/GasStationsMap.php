<?php

namespace App\ApiResource\Controller;

use App\Repository\GasStationRepository;
use App\Service\GasStationsMapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GasStationsMap extends AbstractController
{
    public static string $operationName = 'get_gas_stations_map';

    public function __construct(
        private GasStationRepository $gasStationRepository,
        private GasStationsMapService $gasStationsMapService
    ) {
    }

    public function __invoke(Request $request)
    {
        $latitude = $request->query->get('latitude') ?? 48.856614;
        $longitude = $request->query->get('longitude') ?? 2.3522219;
        $radius = $request->query->get('radius') ?? 10000000000;

        $gasStations = $this->gasStationRepository->getGasStationsMap($longitude, $latitude, $radius, $request->query->all());
        return $gasStations;
    }
}
