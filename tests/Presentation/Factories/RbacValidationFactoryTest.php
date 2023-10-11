<?php

declare(strict_types=1);

namespace Tests\Presentation\Middleware;

use RavineRbac\Data\Protocols\Rbac\ResourceFetcherInterface;
use RavineRbac\Data\Protocols\Rbac\RoleFetcherInterface;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\Resource;
use RavineRbac\Presentation\Factories\RbacValidationFactory;
use RavineRbac\Presentation\Middleware\RoleValidationMiddleware;
use PhpOption\Some;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class RbacValidationFactoryTest extends TestCase
{
    private RbacValidationFactory $sut;
    private MockObject|AccessControl $accessControl;
    public function setUp(): void
    {
        $roleFetcher = $this->createMock(RoleFetcherInterface::class);
        $resourceFetcher = $this->createMock(ResourceFetcherInterface::class);
        $accessControl = $this->createMock(AccessControl::class);
        $this->autowireContainer(RoleFetcherInterface::class, $roleFetcher);
        $this->autowireContainer(ResourceFetcherInterface::class, $resourceFetcher);
        $this->autowireContainer(AccessControl::class, $accessControl);
        $this->accessControl = $accessControl;

        $this->sut = new RbacValidationFactory($this->getContainer());
    }

    public function testWillRetrieveInstanceWithCorrectValues()
    {
        $validator = $this->sut;
        $subject = $validator('video');
        $this->accessControl->method('getResource')->willReturn(
            new Some(
                new Resource('video', 'description')
            )
        );

        $this->accessControl->expects(self::once())->method('getResource')->with('video');

        $this->assertInstanceOf(RoleValidationMiddleware::class, $subject);
        $this->assertSame('video', $subject->getOptionResource()->get()->name);
    }
}