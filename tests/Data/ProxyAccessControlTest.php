<?php

declare(strict_types=1);

namespace Tests\Presentation\Middleware;

use Prophecy\Argument;
use Prophecy\Prophet;
use Psr\Log\LoggerInterface;
use RavineRbac\Data\Proxy\ProxyAccessControl;
use RavineRbac\Domain\Events\EventDispatcher;
use RavineRbac\Domain\Events\Events\OnRoleAppendedEvent;
use RavineRbac\Domain\Events\ListenerInterface;
use RavineRbac\Domain\Events\ListenerProvider;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;
use PHPUnit\Framework\MockObject\MockObject;
use RavineRbac\Domain\OptionalApi\Result\Ok;
use RavineRbac\Domain\Repositories\ResourcesRepositories\ResourceFetcherRepositoryInterface;
use RavineRbac\Domain\Repositories\RolesRepositories\RoleFetcherRepositoryInterface;
use Tests\TestCase;
use function PHPUnit\Framework\assertInstanceOf;

class ProxyAccessControlTest extends TestCase
{
    private Prophet $prophet;
    private ProxyAccessControl $sut;
    private AccessControl $accessControl;
    private MockObject|RoleFetcherRepositoryInterface $roleFetcher;
    private MockObject|ResourceFetcherRepositoryInterface $resourceFetcher;

    private ListenerProvider $listenerProvider;

    public function setUp(): void
    {
        $this->prophet = new Prophet();
        $this->accessControl = new AccessControl();
        $logger = $this->createMock(LoggerInterface::class);
        $roleFetcher = $this->createMock(RoleFetcherRepositoryInterface::class);
        $resourceFetcher = $this->createMock(ResourceFetcherRepositoryInterface::class);
        $this->listenerProvider = new ListenerProvider();

        $this->roleFetcher = $roleFetcher;
        $this->resourceFetcher = $resourceFetcher;

        $this->sut = new ProxyAccessControl(
            $this->accessControl,
            new EventDispatcher($this->listenerProvider),
            $logger
        );
    }

    public function testShouldRetrieveRoleIfItExistsInAccessControl()
    {
        $this->accessControl->forgeRole('common', 'description');
        $role = $this->sut->getRole('common')->get();
        $this->assertEquals($role->name, 'common');
        $this->assertInstanceOf(Role::class, $role);
    }

    public function testShouldRetrieveRoleIfItDoesNOTExistInAccessControlButIsAvailableInRoleFetcher()
    {
        $this->roleFetcher->method('fetch')->willReturn(new Ok(new Role('common', 'description')));
        $this->sut->setRoleFetcherRepository($this->roleFetcher);
        $role = $this->sut->getRole('common')->get();
        $this->assertEquals($role->name, 'common');
        $this->assertInstanceOf(Role::class, $role);
    }

    public function testShouldReturnNothingWhenUnavailableRole()
    {
        $this->roleFetcher->method('fetch')->willReturn(new Ok(null));
        $role = $this->sut->getRole('common');
        $this->assertTrue($role->isEmpty());
    }
    public function testShouldRetrieveResourceIfItExistsInAccessControl()
    {
        $this->accessControl->createResourceType('video', 'description');
        $resource = $this->sut->getResourceType('video')->get();
        $this->assertEquals($resource->name, 'video');
        $this->assertInstanceOf(ResourceType::class, $resource);
    }

    public function testShouldRetrieveResourceIfItDoesNOTExistInAccessControlButIsAvailableInRoleFetcher()
    {
        $this->resourceFetcher->method('fetch')->willReturn(
            new Ok(new ResourceType('video', 'description'))
        );
        $this->sut->setResourceFetcherRepository($this->resourceFetcher);
        $resource = $this->sut->getResourceType('video')->get();
        $this->assertEquals($resource->name, 'video');
        $this->assertInstanceOf(ResourceType::class, $resource);
    }

    public function testShouldReturnNothingWhenUnavailableResource()
    {
        $this->resourceFetcher->method('fetch')->willReturn(new Ok(null));
        $resource = $this->sut->getResourceType('');
        $this->assertTrue($resource->isEmpty());
    }

    public function testShouldCallListenerFromProvider()
    {
        $observer = $this->prophet->prophesize()->willImplement(ListenerInterface::class);
        $observer->execute(Argument::type(OnRoleAppendedEvent::class))->will(
            function ($args) {
                $event = array_pop($args);
                assertInstanceOf(OnRoleAppendedEvent::class, $event);
            }
        )->shouldBeCalled();
        $role = new Role('Name', 'Description');

        $this->listenerProvider->addListener(
            OnRoleAppendedEvent::class,
            $observer->reveal()
        );

        $this->sut->appendRole($role);

        $this->prophet->checkPredictions();
    }
}