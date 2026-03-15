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
    public function __construct(private readonly EventService $eventService)
    {
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
        $this->assertOwner($event);

        return $this->json($this->eventService->serialize($event));
    }

    #[Route('/{id}', name: 'update', requirements: ['id' => '\\d+'], methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Event $event): JsonResponse
    {
        $this->assertOwner($event);
        $data = json_decode($request->getContent(), true) ?? [];
        $payload = $this->eventService->update($event, $data);
        return $this->json($payload);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => '\\d+'], methods: ['DELETE'])]
    public function delete(Event $event): JsonResponse
    {
        $this->assertOwner($event);
        $this->eventService->delete($event);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    // ── Private helpers ───────────────────────────────────────

    private function getOwnerId(): string
    {
        /** @var JwtUser $user */
        $user = $this->getUser();
        return $user->getUserId();
    }

    private function assertOwner(Event $event): void
    {
        if ($event->getOwnerId() !== $this->getOwnerId()) {
            throw $this->createAccessDeniedException('You do not own this event.');
        }
    }
}
