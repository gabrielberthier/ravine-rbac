<?php

declare(strict_types=1);

namespace Tests\Presentation\Middleware;

use RavineRbac\Application\Factories\RbacValidationFactory;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class RbacValidationFactoryTest extends TestCase
{
    private RbacValidationFactory $sut;
    private MockObject|AccessControl $accessControl;
    // public function setUp(): void
    // {
    //     $this->sut = new RbacValidationFactory($this->createMockedPsr11Container());
    // }

    // public function testWillRetrieveInstanceWithCorrectValues()
    // {
    //     $validator = $this->sut;
    //     $subject = $validator('video');
    //     $this->accessControl->method('getResourceType')->willReturn(
    //         new Some(
    //             new ResourceType('video', 'description')
    //         )
    //     );

    //     $this->accessControl->expects(self::once())->method('getResourceType')->with('video');

    //     $this->assertInstanceOf(RoleValidationMiddleware::class, $subject);
    //     $this->assertSame('video', $subject->getOptionResource()->get()->name);
    // }

    // private function createMockedPsr11Container(): ContainerInterface
    // {
    //     $prophet = new Prophet();
    //     $prophecy = $prophet->prophesize()->willImplement(ContainerInterface::class);
        
    //     $roleFetcher = $this->createMock(RoleFetcherInterface::class);
    //     $resourceFetcher = $this->createMock(ResourceFetcherInterface::class);
    //     $accessControl = $this->createMock(AccessControl::class);
        
    //     $prophecy->get(RoleFetcherInterface::class)->willReturn($roleFetcher);
    //     $prophecy->get(ResourceFetcherInterface::class)->willReturn($resourceFetcher);
    //     $prophecy->get(AccessControl::class)->willReturn($accessControl);

    //     $this->accessControl = $accessControl;

    //     return $prophecy->reveal();
    // }
}