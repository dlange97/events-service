<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RouteRepository;
use App\Traits\HasInstanceId;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
#[ORM\Table(name: 'route')]
#[ORM\Index(columns: ['owner_id'])]
#[ORM\Index(columns: ['event_id'])]
#[ORM\HasLifecycleCallbacks]
class Route
{
    use HasInstanceId;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Route name is required.')]
    #[Assert\Length(max: 255, maxMessage: 'Route name cannot exceed 255 characters.')]
    private string $name = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: 'json')]
    #[Assert\NotNull(message: 'Route path is required.')]
    private array $geoJson = [];

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $distanceMeters = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $durationMinutes = null;

    #[ORM\Column(type: 'string', length: 16, options: ['default' => '#6366f1'])]
    private string $color = '#6366f1';

    /**
     * @var array<int, array<int, float|int|string>>
     */
    #[ORM\Column(type: 'json')]
    private array $waypoints = [];

    #[ORM\Column(type: 'string', length: 36)]
    private string $ownerId = '';

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $eventId = null;

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

    /**
     * @return array<string, mixed>
     */
    public function getGeoJson(): array
    {
        return $this->geoJson;
    }

    /**
     * @param array<string, mixed> $geoJson
     */
    public function setGeoJson(array $geoJson): static
    {
        $this->geoJson = $geoJson;
        return $this;
    }

    public function getDistanceMeters(): ?float
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(?float $distanceMeters): static
    {
        $this->distanceMeters = $distanceMeters;
        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(?int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;
        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $normalized = strtoupper(trim($color));
        if (!preg_match('/^#[0-9A-F]{6}$/', $normalized)) {
            $normalized = '#6366F1';
        }

        $this->color = $normalized;

        return $this;
    }

    /**
     * @return array<int, array<int, float|int|string>>
     */
    public function getWaypoints(): array
    {
        return $this->waypoints;
    }

    /**
     * @param array<int, array<int, float|int|string>> $waypoints
     */
    public function setWaypoints(array $waypoints): static
    {
        $this->waypoints = $waypoints;
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

    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    public function setEventId(?int $eventId): static
    {
        $this->eventId = $eventId;
        return $this;
    }
}
