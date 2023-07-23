<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Entity\Traits\NameTraits;
use App\Repository\GasTypeRepository;
use App\Service\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: GasTypeRepository::class)]
#[ApiResource]
class GasType
{
    use IdentifyTraits;
    use NameTraits;
    use TimestampableEntity;
    use BlameableEntity;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }
}
