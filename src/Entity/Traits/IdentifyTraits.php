<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait IdentifyTraits
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    #[Groups(['get_gas_types'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, length: 36)]
    #[ApiProperty(identifier: true)]
    #[Groups(['get_gas_stations', 'get_gas_types', 'get_gas_station'])]
    private ?string $uuid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }
}
