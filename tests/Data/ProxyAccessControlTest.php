<?php

declare(strict_types=1);

namespace Tests\Presentation\Middleware;

use Psr\Log\LoggerInterface;
use RavineRbac\Application\Exceptions\HttpForbiddenAccessException;
use RavineRbac\Application\Middleware\RoleValidationMiddleware;
use RavineRbac\Data\Proxy\ProxyAccessControl;
use RavineRbac\Domain\Events\EventDispatcher;
use RavineRbac\Domain\Events\ListenerProvider;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\ContextIntent;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Application\Protocols\RbacFallbackInterface;
use Nyholm\Psr7\Response;
use PhpOption\Option;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use RavineRbac\Domain\OptionalApi\Result\Ok;
use RavineRbac\Domain\Repositories\ResourcesRepositories\ResourceFetcherRepositoryInterface;
use RavineRbac\Domain\Repositories\RolesRepositories\RoleFetcherRepositoryInterface;
use Tests\TestCase;

class ProxyAccessControlTest extends TestCase
{
    private \Prophecy\Prophet $prophet;
    private ProxyAccessControl $sut;
    private AccessControl $accessControl;
    private MockObject|RoleFetcherRepositoryInterface $roleFetcher;
    private MockObject|ResourceFetcherRepositoryInterface $resourceFetcher;

    public function setUp(): void
    {
        $this->accessControl = new AccessControl();
        $logger = $this->createMock(LoggerInterface::class);
        $roleFetcher = $this->createMock(RoleFetcherRepositoryInterface::class);
        $resourceFetcher = $this->createMock(ResourceFetcherRepositoryInterface::class);

        $this->roleFetcher = $roleFetcher;
        $this->resourceFetcher = $resourceFetcher;

        $this->sut = new ProxyAccessControl(
            $this->accessControl,
            new EventDispatcher(new ListenerProvider()),
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
}