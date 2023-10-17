<?php

declare(strict_types=1);

namespace Tests\Presentation\Middleware;

use RavineRbac\Application\Factories\RbacValidationFactory;
use RavineRbac\Application\Middleware\RoleValidationMiddleware;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class RbacValidationFactoryTest extends TestCase
{
    private RbacValidationFactory $sut;
    private MockObject|AccessControl $accessControl;
    public function setUp(): void
    {
        
        $this->accessControl = $this->createMock(AccessControl::class);;

        $this->sut = new RbacValidationFactory($this->accessControl);
    }

    public function testWillRetrieveInstanceWithCorrectValues()
    {
        $subject = $this->sut->__invoke('video');

        $this->assertInstanceOf(RoleValidationMiddleware::class, $subject);
    }
}