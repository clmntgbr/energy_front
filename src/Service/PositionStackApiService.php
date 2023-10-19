<?php

namespace App\Service;

use App\Entity\Address;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Safe\Exceptions\JsonException;

class PositionStackApiService
{
    public function __construct(
        private readonly string $positionStackApiKey,
        private readonly string $positionStackUrl
    ) {
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function forward(Address $address): ?array
    {
        $client = new Client();
        $url = sprintf('%sforward?access_key=%s&query=%s', $this->positionStackUrl, $this->positionStackApiKey, urlencode($address->getVicinity()));

        try {
            $response = $client->request('GET', $url);
        } catch (\Exception $exception) {
            return ['message' => $exception->getMessage(), 'forwardUrl' => $url];
        }

        $data = \Safe\json_decode($response->getBody()->getContents(), true);

        if (!array_key_exists('data', $data)) {
            return null;
        }

        if (!array_key_exists(0, $data['data'])) {
            return null;
        }

        $datum = $data['data'][0];
        $datum['forwardUrl'] = $url;
        $datum['oldVicinity'] = $address->getVicinity();

        return $datum;
    }

    public function reverse(Address $address): ?array
    {
        $client = new Client();
        $url = sprintf('%sreverse?access_key=%s&query=%s,%s', $this->positionStackUrl, $this->positionStackApiKey, $address->getLatitude(), $address->getLongitude());

        try {
            $response = $client->request('GET', $url);
        } catch (\Exception $exception) {
            return ['message' => $exception->getMessage(), 'reverseUrl' => $url];
        }

        $data = \Safe\json_decode($response->getBody()->getContents(), true);

        if (!array_key_exists('data', $data)) {
            return null;
        }

        if (!array_key_exists(0, $data['data'])) {
            return null;
        }

        $datum = $data['data'][0];
        $datum['reverseUrl'] = $url;
        $datum['oldVicinity'] = $address->getVicinity();

        return $datum;
    }
}
