<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Route as RouteEntity;
use MyDashboard\Shared\Security\JwtUser;
use App\Service\RouteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/events/routes', name: 'events_routes_')]
class RouteController extends AbstractController
{
    public function __construct(private readonly RouteService $routeService)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $routes = $this->routeService->findAllByOwner($this->getOwnerId());
        return $this->json($routes);
    }

    #[Route('/event/{eventId}', name: 'by_event', methods: ['GET'])]
    public function byEvent(int $eventId): JsonResponse
    {
        $routes = $this->routeService->findByEvent($this->getOwnerId(), $eventId);
        return $this->json($routes);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        try {
            $route = $this->routeService->create($this->getOwnerId(), $data);
            return $this->json($route, Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, RouteEntity $entity): JsonResponse
    {
        $this->assertOwner($entity);
        $data = json_decode($request->getContent(), true) ?? [];

        try {
            $this->routeService->update($entity, $data);
            return $this->json($this->routeService->serialize($entity));
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(RouteEntity $entity): JsonResponse
    {
        $this->assertOwner($entity);
        $this->routeService->delete($entity);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function getOwnerId(): string
    {
        /** @var JwtUser $user */
        $user = $this->getUser();
        return $user->getUserId();
    }

    private function assertOwner(RouteEntity $entity): void
    {
        if ($entity->getOwnerId() !== $this->getOwnerId()) {
            throw $this->createAccessDeniedException('You do not own this route.');
        }
    }
}
