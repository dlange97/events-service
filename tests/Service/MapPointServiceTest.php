<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\MapPoint;
use App\Repository\MapPointRepository;
use App\Service\MapPointService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MapPointServiceTest extends TestCase
{
    private MapPointRepository&MockObject $repo;
    private ValidatorInterface&MockObject $validator;
    private MapPointService $service;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(MapPointRepository::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->service = new MapPointService($this->repo, $this->validator);
    }

    public function testFindAllByOwnerReturnsSerializedPoints(): void
    {
        $point = $this->makePoint(5, 'Start', 'owner-1');

        $this->repo->expects($this->once())
            ->method('findAllByOwner')
            ->with('owner-1')
            ->willReturn([$point]);

        $result = $this->service->findAllByOwner('owner-1');

        $this->assertCount(1, $result);
        $this->assertSame('Start', $result[0]['name']);
        $this->assertSame(5, $result[0]['id']);
    }

    public function testCreatePersistsPoint(): void
    {
        $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->repo->expects($this->once())->method('save');

        $payload = $this->service->create('owner-2', [
            'name' => '  Punkt  ',
            'description' => 'Opis',
            'lat' => 50.0647,
            'lon' => 19.945,
        ]);

        $this->assertSame('Punkt', $payload['name']);
        $this->assertSame(50.0647, $payload['lat']);
        $this->assertSame(19.945, $payload['lon']);
    }

    public function testUpdateChangesFields(): void
    {
        $point = $this->makePoint(2, 'Old', 'owner-1');
        $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->repo->expects($this->once())->method('save');

        $payload = $this->service->update($point, [
            'name' => 'Nowa nazwa',
            'description' => null,
            'lat' => 52.22,
            'lon' => 21.01,
        ]);

        $this->assertSame('Nowa nazwa', $payload['name']);
        $this->assertNull($payload['description']);
        $this->assertSame(52.22, $payload['lat']);
    }

    public function testDeleteCallsRepository(): void
    {
        $point = $this->makePoint(1, 'To delete', 'owner-1');

        $this->repo->expects($this->once())
            ->method('remove')
            ->with($point, true);

        $this->service->delete($point);
    }

    public function testAssertOwnerPassesForCorrectOwner(): void
    {
        $point = $this->makePoint(1, 'Mine', 'owner-1');

        // Should not throw any exception
        $this->service->assertOwner($point, 'owner-1');
        $this->addToAssertionCount(1);
    }

    public function testAssertOwnerThrowsAccessDeniedForWrongOwner(): void
    {
        $point = $this->makePoint(1, 'Mine', 'owner-1');

        $this->expectException(AccessDeniedHttpException::class);

        $this->service->assertOwner($point, 'owner-2');
    }

    private function makePoint(int $id, string $name, string $ownerId): MapPoint
    {
        $point = new MapPoint();
        $point->setName($name);
        $point->setDescription('desc');
        $point->setLat(50.0);
        $point->setLon(19.0);
        $point->setOwnerId($ownerId);

        $ref = new \ReflectionProperty(MapPoint::class, 'id');
        $ref->setAccessible(true);
        $ref->setValue($point, $id);

        return $point;
    }
}
