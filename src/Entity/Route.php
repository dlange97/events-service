<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RouteRepository;
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
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /** Name/title of the route */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Route name is required.')]
    #[Assert\Length(max: 255, maxMessage: 'Route name cannot exceed 255 characters.')]
    private string $name = '';

    /** Route description */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** GeoJSON LineString or MultiLineString representing the route path */
    #[ORM\Column(type: 'json')]
    #[Assert\NotNull(message: 'Route path is required.')]
    private array $geoJson = [];

    /** Total route distance in meters (calculated from GeoJSON) */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $distanceMeters = null;

    /** Estimated duration in minutes */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $durationMinutes = null;

    /** JSON array of waypoints: [["lat", "lon"], ...] */
    #[ORM\Column(type: 'json')]
    private array $waypoints = [];

    /** UUID string from JWT – identifier of route owner */
    #[ORM\Column(type: 'string', length: 36)]
    private string $ownerId = '';

    /** Optional: associated event ID */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $eventId = null;

    // ── Getters / Setters ──

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

    public function getGeoJson(): array
    {
        return $this->geoJson;
    }

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

    public function getWaypoints(): array
    {
        return $this->waypoints;
    }

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
