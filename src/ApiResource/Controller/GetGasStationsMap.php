<?php

namespace App\ApiResource\Controller;

use App\Repository\GasStationRepository;
use App\Service\GasStationsMapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetGasStationsMap extends AbstractController
{
    public static string $operationName = 'get_gas_stations_map';

    public function __construct(
        private readonly GasStationRepository $gasStationRepository,
        private readonly GasStationsMapService $gasStationsMapService
    ) {
    }

    public function __invoke(Request $request)
    {
        $latitude = $request->query->get('latitude') ?? 48.764977;
        $longitude = $request->query->get('longitude') ?? 2.358192;
        $radius = $request->query->get('radius') ?? 500000;
        $gasTypeUuid = $request->query->get('gasTypeUuid') ?? '1';
        $filterCity = $request->query->get('filter_city') ?? null;
        $filterDepartment = $request->query->get('filter_department') ?? null;

        $gasStations = $this->gasStationRepository->getGasStationsMap($longitude, $latitude, $radius, $gasTypeUuid, $filterCity, $filterDepartment);

        return $this->gasStationsMapService->invoke($gasStations, $gasTypeUuid);
    }
}
