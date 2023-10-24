<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\ApiResource\Controller\GetAddressCities;
use App\ApiResource\Controller\GetAddressDepartments;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\AddressRepository;
use App\Service\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get_address_cities' => [
            'method' => 'GET',
            'path' => '/address/cities',
            'controller' => GetAddressCities::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['get_addresses_cities', 'common']],
        ],
        'get_address_departments' => [
            'method' => 'GET',
            'path' => '/address/departments',
            'controller' => GetAddressDepartments::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['get_addresses_departments', 'common']],
        ],
    ],
    itemOperations: ['get'],
)]
class Address
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_stations', 'get_gas_station'])]
    private ?string $vicinity = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_stations', 'get_gas_station'])]
    private ?string $street;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_stations', 'get_gas_station'])]
    private ?string $number = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_stations', 'get_addresses', 'get_gas_station'])]
    private ?string $city;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_stations', 'get_addresses', 'get_gas_station'])]
    private ?string $region = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_stations', 'get_gas_station'])]
    private ?string $postalCode;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_stations', 'get_gas_station'])]
    private ?string $country;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_stations', 'get_gas_station'])]
    private ?string $longitude;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_stations', 'get_gas_station'])]
    private ?string $latitude;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $positionStackApiResult;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function __toString()
    {
        return $this->vicinity ?? $this->street;
    }

    public function getVicinity(): ?string
    {
        return $this->vicinity;
    }

    public function setVicinity(?string $vicinity): static
    {
        $this->vicinity = $vicinity;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getPositionStackApiResult(): array
    {
        return $this->positionStackApiResult ?? [];
    }

    public function getPositionStackApiResultAdmin(): string
    {
        return json_encode($this->positionStackApiResult, JSON_PRETTY_PRINT);
    }

    public function setPositionStackApiResult(?array $positionStackApiResult): self
    {
        $this->positionStackApiResult = $positionStackApiResult;

        return $this;
    }
}
