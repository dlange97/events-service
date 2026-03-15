<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Route;
use App\Repository\RouteRepository;
use App\Service\RouteService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RouteServiceTest extends TestCase
{
    private RouteRepository&MockObject $repo;
    private ValidatorInterface&MockObject $validator;
    private RouteService $service;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(RouteRepository::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->service = new RouteService($this->repo, $this->validator);
    }

    public function testCreateUsesProvidedColor(): void
    {
        $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->repo->expects($this->once())->method('save');

        $result = $this->service->create('owner-1', [
            'name' => 'Test route',
            'geoJson' => [
                'type' => 'FeatureCollection',
                'features' => [],
            ],
            'waypoints' => [],
            'color' => '#22c55e',
        ]);

        $this->assertSame('#22C55E', $result['color']);
    }

    public function testUpdateFallsBackToDefaultColorWhenInvalid(): void
    {
        $route = new Route();
        $route->setOwnerId('owner-1');
        $route->setName('Existing route');
        $route->setGeoJson([
            'type' => 'FeatureCollection',
            'features' => [],
        ]);
        $route->setWaypoints([]);

        $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->repo->expects($this->once())->method('save');

        $result = $this->service->update($route, [
            'color' => 'bad-color',
        ]);

        $this->assertSame('#6366F1', $result['color']);
    }
}
