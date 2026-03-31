<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use MyDashboard\Shared\Security\JwtUser;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/events', name: 'events_')]
class EventController extends AbstractController
{
    public function __construct(
        private readonly EventService $eventService,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->eventService->findAllByOwner($this->getOwnerId()));
    }

    #[Route('/upcoming', name: 'upcoming', methods: ['GET'])]
    public function upcoming(): JsonResponse
    {
        return $this->json($this->eventService->findUpcoming($this->getOwnerId()));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $payload = $this->eventService->create($this->getOwnerId(), $data);
        return $this->json($payload, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(Event $event): JsonResponse
    {
        $this->eventService->assertAccessible($event, $this->getOwnerId());

        return $this->json($this->eventService->serialize($event));
    }

    #[Route('/{id}', name: 'update', requirements: ['id' => '\\d+'], methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Event $event): JsonResponse
    {
        $this->eventService->assertAccessible($event, $this->getOwnerId());
        $data = json_decode($request->getContent(), true) ?? [];
        $payload = $this->eventService->update($event, $data);
        return $this->json($payload);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => '\\d+'], methods: ['DELETE'])]
    public function delete(Event $event): JsonResponse
    {
        $this->eventService->assertOwner($event, $this->getOwnerId());
        $this->eventService->delete($event);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/share', name: 'share', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function share(Request $request, Event $event): JsonResponse
    {
        $this->eventService->assertOwner($event, $this->getOwnerId());
        $data = json_decode($request->getContent(), true) ?? [];
        $userId = trim((string) ($data['userId'] ?? ''));

        if ($userId === '') {
            return $this->json(['error' => 'Missing required field: userId'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $payload = $this->eventService->shareWithUser($event, $userId);
        } catch (\InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($payload);
    }

    #[Route('/{id}/share/{userId}', name: 'unshare', requirements: ['id' => '\\d+'], methods: ['DELETE'])]
    public function unshare(Event $event, string $userId): JsonResponse
    {
        $this->eventService->assertOwner($event, $this->getOwnerId());

        return $this->json($this->eventService->unshareWithUser($event, $userId));
    }

    // ── Private helpers ───────────────────────────────────────

    private function getOwnerId(): string
    {
        /** @var JwtUser $user */
        $user = $this->getUser();
        return $user->getUserId();
    }
}
