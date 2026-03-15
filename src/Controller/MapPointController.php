<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\MapPoint;
use MyDashboard\Shared\Security\JwtUser;
use App\Service\MapPointService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/events/points', name: 'events_points_')]
class MapPointController extends AbstractController
{
    public function __construct(private readonly MapPointService $mapPointService)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->mapPointService->findAllByOwner($this->getOwnerId()));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        try {
            $point = $this->mapPointService->create($this->getOwnerId(), $data);

            return $this->json($point, Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, MapPoint $point): JsonResponse
    {
        $this->mapPointService->assertOwner($point, $this->getOwnerId());
        $data = json_decode($request->getContent(), true) ?? [];

        try {
            return $this->json($this->mapPointService->update($point, $data));
        } catch (\InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(MapPoint $point): JsonResponse
    {
        $this->mapPointService->assertOwner($point, $this->getOwnerId());
        $this->mapPointService->delete($point);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function getOwnerId(): string
    {
        /** @var JwtUser $user */
        $user = $this->getUser();

        return $user->getUserId();
    }
}
