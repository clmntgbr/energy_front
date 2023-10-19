<?php

namespace App\Service;

use App\Entity\EntityId\GasStationId;
use App\Entity\EntityId\GasTypeId;
use App\Message\CreateGasPriceMessage;
use App\Message\CreateGasStationMessage;
use App\Message\UpdateGasStationMessage;
use App\Repository\GasStationRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class GasPriceUpdateCommandService
{
    public function __construct(
        private readonly string $gasPricePath,
        private readonly string $gasPriceJsonName,
        private readonly GasStationRepository $gasStationRepository,
        private readonly GasStationService $gasStationService,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function invoke(): void
    {
        if (!FileSystemService::exist($this->gasPricePath, $this->gasPriceJsonName)) {
            throw new \Exception('json gas prices dont exist.');
        }

        $file = fopen("$this->gasPricePath/$this->gasPriceJsonName", 'r');

        $content = fread($file, filesize("$this->gasPricePath/$this->gasPriceJsonName"));
        fclose($file);

        $data = json_decode($content, true);

        $gasStations = $this->gasStationRepository->findGasStationsById();

        $count = 0; // dev

        foreach ($data as $datum) {
            $gasStationId = $this->gasStationService->getGasStationId($datum['@attributes']['id']);

            if (!in_array(substr($gasStationId->getId(), 0, 2), ['94', '75'])) { // for dev only
                continue;
            }

            ++$count; // dev

            $hash = $this->getHash($datum);

            if (!array_key_exists($gasStationId->getId(), $gasStations)) {
                $this->createGasStationMessage($gasStationId, $hash, $datum);
            }

            if (array_key_exists($gasStationId->getId(), $gasStations) && $gasStations[$gasStationId->getId()]['hash'] !== $hash) {
                $this->updateGasStationMessage($gasStationId, $hash, $datum);
            }

            $this->createGasPricesMessage($gasStationId, $datum);
            if ($count >= 10) { // dev
                exit; // dev
            } // dev
        }
    }

    private function getHash(array $datum): string
    {
        $element = $datum['services'] ?? [];

        return hash('sha256', json_encode($element));
    }

    private function createGasStationMessage(GasStationId $gasStationId, string $hash, array $datum)
    {
        $this->messageBus->dispatch(
            new CreateGasStationMessage(
                $gasStationId,
                $this->convert($datum['@attributes']['pop'] ?? ''),
                $hash,
                $this->convert($datum['@attributes']['cp'] ?? ''),
                $this->convert($datum['@attributes']['longitude'] ?? ''),
                $this->convert($datum['@attributes']['latitude'] ?? ''),
                $this->convert($datum['adresse'] ?? ''),
                $this->convert($datum['ville'] ?? ''),
                'FRANCE',
                $datum,
            ),
        );
    }

    private function createGasPricesMessage(GasStationId $gasStationId, array $datum)
    {
        foreach ($datum['prix'] ?? [] as $item) {
            $gasTypeDatum = $this->getGasTypeId($datum, $item);
            $this->messageBus->dispatch(
                new CreateGasPriceMessage(
                    $gasStationId,
                    $gasTypeDatum['gasTypeId'],
                    $gasTypeDatum['date'],
                    $gasTypeDatum['value']
                )
            );
        }
    }

    private function getGasTypeId(array $element, array $item): array
    {
        $gasTypeId = new GasTypeId($item['@attributes']['id'] ?? 0);
        $date = $item['@attributes']['maj'] ?? null;
        $value = $item['@attributes']['valeur'] ?? null;

        if (1 == count($element['prix'])) {
            $gasTypeId = new GasTypeId($item['id'] ?? 0);
            $date = $item['maj'] ?? null;
            $value = $item['valeur'] ?? null;
        }

        return ['gasTypeId' => $gasTypeId, 'date' => $date, 'value' => $value];
    }

    private function updateGasStationMessage(GasStationId $gasStationId, string $hash, array $datum)
    {
        $this->messageBus->dispatch(
            new UpdateGasStationMessage(
                $gasStationId,
                $this->convert($datum['@attributes']['pop'] ?? ''),
                $hash,
                $this->convert($datum['@attributes']['cp'] ?? ''),
                $this->convert($datum['@attributes']['longitude'] ?? ''),
                $this->convert($datum['@attributes']['latitude'] ?? ''),
                $this->convert($datum['adresse'] ?? ''),
                $this->convert($datum['ville'] ?? ''),
                'FRANCE',
                $datum,
            ),
        );
    }

    private function convert($datum): string
    {
        if (is_array($datum)) {
            return implode(' ', $datum);
        }

        return $datum;
    }
}
