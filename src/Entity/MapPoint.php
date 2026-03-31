<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MapPointRepository;
use App\Traits\HasInstanceId;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MapPointRepository::class)]
#[ORM\Table(name: 'map_point')]
#[ORM\Index(columns: ['owner_id'])]
#[ORM\HasLifecycleCallbacks]
class MapPoint
{
    use HasInstanceId;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Point name is required.')]
    #[Assert\Length(max: 255, maxMessage: 'Point name cannot exceed 255 characters.')]
    private string $name = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'float')]
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Latitude must be between -90 and 90.')]
    private float $lat = 0.0;

    #[ORM\Column(type: 'float')]
    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Longitude must be between -180 and 180.')]
    private float $lon = 0.0;

    #[ORM\Column(type: 'string', length: 36)]
    private string $ownerId = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = trim($name);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description !== null ? trim($description) : null;

        return $this;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): float
    {
        return $this->lon;
    }

    public function setLon(float $lon): static
    {
        $this->lon = $lon;

        return $this;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): static
    {
        $this->ownerId = $ownerId;

        return $this;
    }
}
