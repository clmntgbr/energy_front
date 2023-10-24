<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Entity\Traits\NameTraits;
use App\Repository\GasStationBrandRepository;
use App\Service\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GasStationBrandRepository::class)]
#[ApiResource]
#[Vich\Uploadable]
class GasStationBrand
{
    use IdentifyTraits;
    use NameTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[Vich\UploadableField(mapping: 'gas_stations_brand_image', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName', dimensions: 'image.dimensions')]
    public ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[Vich\UploadableField(mapping: 'gas_stations_brand_image', fileNameProperty: 'imageLow.name', size: 'imageLow.size', mimeType: 'imageLow.mimeType', originalName: 'imageLow.originalName', dimensions: 'imageLow.dimensions')]
    private ?File $imageLowFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $imageLow;

    public function __construct()
    {
        $this->image = new EmbeddedFile();
        $this->imageLow = new EmbeddedFile();
        $this->uuid = Uuid::v4();
    }

    #[Groups(['get_gas_stations', 'get_gas_station'])]
    public function getImagePath(): string
    {
        return sprintf('/images/gas_stations_brand/%s', $this->getImage()->getName());
    }

    #[Groups(['get_gas_stations', 'get_gas_station'])]
    public function getImageLowPath(): string
    {
        return sprintf('/images/gas_stations_brand/%s', $this->getImageLow()->getName());
    }

    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageLow(): EmbeddedFile
    {
        return $this->imageLow;
    }

    public function setImageLow(EmbeddedFile $image): self
    {
        $this->imageLow = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getImageLowFile(): ?File
    {
        return $this->imageLowFile;
    }

    public function setImageLowFile(File $imageFile = null): self
    {
        $this->imageLowFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }
}
