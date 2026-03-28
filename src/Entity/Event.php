<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EventRepository;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'event')]
#[ORM\HasLifecycleCallbacks]
class Event
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Title is required.')]
    #[Assert\Length(max: 255, maxMessage: 'Title cannot exceed 255 characters.')]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull(message: 'Start date/time is required.')]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    private ?string $locationName = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $locationLat = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $locationLon = null;

    /** UUID string from JWT – no foreign-key constraint (cross-service). */
    #[ORM\Column(type: 'string', length: 36)]
    private string $ownerId = '';

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $sharedWithUserIds = [];

    // ── Getters / Setters ─────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = trim($title);
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

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getLocationName(): ?string
    {
        return $this->locationName;
    }

    public function setLocationName(?string $locationName): static
    {
        $this->locationName = $locationName;
        return $this;
    }

    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    public function setLocationLat(?float $locationLat): static
    {
        $this->locationLat = $locationLat;
        return $this;
    }

    public function getLocationLon(): ?float
    {
        return $this->locationLon;
    }

    public function setLocationLon(?float $locationLon): static
    {
        $this->locationLon = $locationLon;
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

    /** @return list<string> */
    public function getSharedWithUserIds(): array
    {
        return array_values(array_unique(array_filter(
            $this->sharedWithUserIds,
            static fn(mixed $value): bool => is_string($value) && trim($value) !== '',
        )));
    }

    /** @param list<string> $userIds */
    public function setSharedWithUserIds(array $userIds): static
    {
        $normalized = [];
        foreach ($userIds as $userId) {
            $trimmed = trim((string) $userId);
            if ($trimmed === '') {
                continue;
            }
            if (!in_array($trimmed, $normalized, true)) {
                $normalized[] = $trimmed;
            }
        }

        $this->sharedWithUserIds = $normalized;

        return $this;
    }

    public function addSharedUserId(string $userId): static
    {
        $trimmed = trim($userId);
        if ($trimmed === '') {
            return $this;
        }

        $shared = $this->getSharedWithUserIds();
        if (!in_array($trimmed, $shared, true)) {
            $shared[] = $trimmed;
            $this->sharedWithUserIds = $shared;
        }

        return $this;
    }

    public function removeSharedUserId(string $userId): static
    {
        $trimmed = trim($userId);
        if ($trimmed === '') {
            return $this;
        }

        $this->sharedWithUserIds = array_values(array_filter(
            $this->getSharedWithUserIds(),
            static fn(string $existing): bool => $existing !== $trimmed,
        ));

        return $this;
    }
}
