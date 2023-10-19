<?php

namespace App\Service;

use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Message\CreateGooglePlaceAnomalyMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final class GooglePlaceService
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    /**
     * @param array<int, GasStation> $gasStations
     */
    public function createAnomalies(array $gasStations): bool
    {
        foreach ($gasStations as $gasStationAnomaly) {
            $this->messageBus->dispatch(
                new CreateGooglePlaceAnomalyMessage(
                    new GasStationId($gasStationAnomaly->getGasStationId())
                )
            );
        }

        return true;
    }

    /**
     * @param array<mixed> $details
     */
    public function updateGasStationGooglePlace(GasStation $gasStation, array $details): void
    {
        $gasStation
            ->getGooglePlace()
            ->setGoogleId($details['id'] ?? null)
            ->setPlaceId($details['place_id'] ?? null)
            ->setBusinessStatus($details['business_status'] ?? null)
            ->setIcon($details['icon'] ?? null)
            ->setPhoneNumber($details['international_phone_number'] ?? null)
            ->setCompoundCode($details['plus_code']['compound_code'] ?? null)
            ->setGlobalCode($details['plus_code']['global_code'] ?? null)
            ->setGoogleRating($details['rating'] ?? null)
            ->setRating($details['rating'] ?? null)
            ->setReference($details['reference'] ?? null)
            ->setOpeningHours($details['opening_hours']['weekday_text'] ?? [])
            ->setUserRatingsTotal($details['user_ratings_total'] ?? null)
            ->setUrl($details['url'] ?? null)
            ->setWebsite($details['website'] ?? null)
            ->setWheelchairAccessibleEntrance($details['wheelchair_accessible_entrance'] ?? false);
    }

    /**
     * @param array<mixed> $details
     */
    public function updateGasStationAddress(GasStation $gasStation, array $details): void
    {
        $address = $gasStation->getAddress();

        foreach ($details['address_components'] as $component) {
            foreach ($component['types'] as $type) {
                switch ($type) {
                    case 'street_number':
                        $address->setNumber($component['long_name']);
                        break;
                    case 'route':
                        $address->setStreet($component['long_name']);
                        break;
                    case 'locality':
                        $address->setCity($component['long_name']);
                        break;
                    case 'administrative_area_level_1':
                        $address->setRegion($component['long_name']);
                        break;
                    case 'country':
                        $address->setCountry($component['long_name']);
                        break;
                    case 'postal_code':
                        $address->setPostalCode($component['long_name']);
                        break;
                }
            }
        }

        $address
            ->setVicinity(sprintf('%s %s, %s %s', $address->getNumber(), $address->getStreet(), $address->getPostalCode(), $address->getCity()))
            ->setLongitude($details['geometry']['location']['lng'] ?? null)
            ->setLatitude($details['geometry']['location']['lat'] ?? null);
    }
}
