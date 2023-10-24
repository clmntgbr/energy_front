<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\GooglePlaceRepository;
use App\Service\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GooglePlaceRepository::class)]
#[ApiResource]
class GooglePlace
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::STRING, length: 15, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $googleId = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $website = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $placeId = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $compoundCode = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $globalCode = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $googleRating = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $rating = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $userRatingsTotal = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $icon = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $reference = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $wheelchairAccessibleEntrance = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_gas_station'])]
    private ?string $businessStatus = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['get_gas_station'])]
    private array $openingHours = [];

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $textsearchApiResult = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $placeDetailsApiResult = null;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function __toString()
    {
        return (string) $this->id;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): static
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    public function setPlaceId(?string $placeId): static
    {
        $this->placeId = $placeId;

        return $this;
    }

    public function getCompoundCode(): ?string
    {
        return $this->compoundCode;
    }

    public function setCompoundCode(?string $compoundCode): static
    {
        $this->compoundCode = $compoundCode;

        return $this;
    }

    public function getGlobalCode(): ?string
    {
        return $this->globalCode;
    }

    public function setGlobalCode(?string $globalCode): static
    {
        $this->globalCode = $globalCode;

        return $this;
    }

    public function getGoogleRating(): ?string
    {
        return $this->googleRating;
    }

    public function setGoogleRating(?string $googleRating): static
    {
        $this->googleRating = $googleRating;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getUserRatingsTotal(): ?string
    {
        return $this->userRatingsTotal;
    }

    public function setUserRatingsTotal(?string $userRatingsTotal): static
    {
        $this->userRatingsTotal = $userRatingsTotal;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getWheelchairAccessibleEntrance(): ?string
    {
        return $this->wheelchairAccessibleEntrance;
    }

    public function setWheelchairAccessibleEntrance(?string $wheelchairAccessibleEntrance): static
    {
        $this->wheelchairAccessibleEntrance = $wheelchairAccessibleEntrance;

        return $this;
    }

    public function getBusinessStatus(): ?string
    {
        return $this->businessStatus;
    }

    public function setBusinessStatus(?string $businessStatus): static
    {
        $this->businessStatus = $businessStatus;

        return $this;
    }

    public function getOpeningHours(): ?array
    {
        return $this->openingHours;
    }

    public function setOpeningHours(?array $openingHours): static
    {
        $this->openingHours = $openingHours;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getTextsearchApiResultAdmin()
    {
        return json_encode($this->textsearchApiResult, JSON_PRETTY_PRINT);
    }

    public function getPlaceDetailsApiResultAdmin()
    {
        return json_encode($this->placeDetailsApiResult, JSON_PRETTY_PRINT);
    }

    public function getPlaceDetailsApiResult(): ?array
    {
        return $this->placeDetailsApiResult;
    }

    public function setPlaceDetailsApiResult(?array $placeDetailsApiResult): self
    {
        $this->placeDetailsApiResult = $placeDetailsApiResult;

        return $this;
    }

    public function getTextsearchApiResult(): ?array
    {
        return $this->textsearchApiResult;
    }

    public function setTextsearchApiResult(?array $textsearchApiResult): self
    {
        $this->textsearchApiResult = $textsearchApiResult;

        return $this;
    }
}
