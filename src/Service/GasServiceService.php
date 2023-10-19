<?php

namespace App\Service;

use App\Entity\EntityId\GasStationId;
use App\Entity\GasService;
use App\Entity\GasStation;
use App\Repository\GasServiceRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

final class GasServiceService
{
    public function __construct(
        private readonly GasServiceRepository $gasServiceRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function getGasStationId($gasStationId): GasStationId
    {
        if (empty($gasStationId)) {
            throw new \Exception();
        }

        return new GasStationId($gasStationId);
    }

    public function deleteGasServices(GasStation $gasStation)
    {
        foreach ($gasStation->getGasServices() as $service) {
            $gasStation->removeGasService($service);
        }

        $this->em->persist($gasStation);
        $this->em->flush();
    }

    public function createGasServices(GasStation $gasStation, array $services)
    {
        if (!array_key_exists('service', $services['services'])) {
            return;
        }

        if (is_array($services['services']['service'])) {
            foreach ($services['services']['service'] as $item) {
                $this->getGasServices($gasStation, $item);
            }

            return;
        }

        if (is_string($services['services']['service'])) {
            $this->getGasServices($gasStation, $services['services']['service']);
        }
    }

    private function getGasServices(GasStation $gasStation, string $label)
    {
        $gasService = $this->gasServiceRepository->findOneBy(['name' => $label]);

        if ($gasService instanceof GasService) {
            if ($gasStation->hasGasService($gasService)) {
                throw new UnrecoverableMessageHandlingException(sprintf('Gas Service is already linked to this Gas Station (Gas Service Name : %s, Gas Station id : %s)', $label, $gasStation->getGasStationId()));
            }
        }

        if (null === $gasService) {
            $gasService = new GasService();
            $gasService
                ->setName($label)
                ->setReference((new Slugify())->slugify($label, '_'));
        }

        $gasStation->addGasService($gasService);
    }
}
