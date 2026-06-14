<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Repository\SecurityLogRepository;
use App\Service\SecurityLogService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SecurityLogServiceTest extends TestCase
{
    private SecurityLogRepository&MockObject $repository;
    private SecurityLogService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SecurityLogRepository::class);
        $this->service    = new SecurityLogService($this->repository);
    }

    public function testGetPaginatedListReturnsMappedItems(): void
    {
        $this->repository->method('countAll')->willReturn(2);
        $this->repository->method('findPaginated')->with(50, 0)->willReturn([
            ['id' => '5', 'ip' => '10.0.0.1', 'path' => '/events', 'method' => 'GET', 'instance_id' => 'inst-1', 'is_sensitive' => '0', 'user_agent' => 'test', 'created_at' => '2026-01-01 00:00:00'],
        ]);

        $result = $this->service->getPaginatedList(1, 50, 'events');

        $this->assertSame('events', $result['service']);
        $this->assertSame(2, $result['total']);
        $this->assertCount(1, $result['items']);
        $this->assertSame(5, $result['items'][0]['id']);
        $this->assertFalse($result['items'][0]['isSensitive']);
    }

    public function testGetPaginatedListClampsPageAndPerPage(): void
    {
        $this->repository->method('countAll')->willReturn(0);
        $this->repository->expects($this->once())->method('findPaginated')->with(100, 0)->willReturn([]);

        $this->service->getPaginatedList(0, 9999, 'events');
    }

    public function testGetPaginatedListCalculatesOffsetCorrectly(): void
    {
        $this->repository->method('countAll')->willReturn(100);
        $this->repository->expects($this->once())->method('findPaginated')->with(10, 20)->willReturn([]);

        $this->service->getPaginatedList(3, 10, 'events');
    }

    public function testClearDelegatesAndReturnsCount(): void
    {
        $this->repository->expects($this->once())->method('clearAll')->willReturn(7);

        $this->assertSame(7, $this->service->clear());
    }
}
