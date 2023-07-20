<?php

namespace App\Entity;

use App\Repository\GasServiceRepository;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Entity\Traits\NameTraits;
use App\Service\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\Blameable;
use Gedmo\Timestampable\Traits\Timestampable;

#[ORM\Entity(repositoryClass: GasServiceRepository::class)]
#[ApiResource]
class GasService
{
    use IdentifyTraits;
    use NameTraits;
    use Timestampable;
    use Blameable;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }
}