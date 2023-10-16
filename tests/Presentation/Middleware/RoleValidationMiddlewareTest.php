<?php

declare(strict_types=1);

namespace Tests\Presentation\Middleware;

use RavineRbac\Application\Exceptions\HttpForbiddenAccessException;
use RavineRbac\Application\Middleware\RoleValidationMiddleware;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\ContextIntent;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Application\Protocols\RbacFallbackInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Tests\TestCase;

class RoleValidationMiddlewareTest extends TestCase
{
    private \Prophecy\Prophet $prophet;
    private RoleValidationMiddleware $sut;
    private AccessControl $accessControl;

    public function setUp(): void
    {
        $this->accessControl = new AccessControl();

        $this->sut = new RoleValidationMiddleware(
            $this->accessControl,
            resource: "video"
        );

    }

    public function testShouldReceivePredefinedPermissionIfItIsSet()
    {
        $this->sut->setPredefinedPermission(new Permission('file requests', ContextIntent::CUSTOM));
        $permission = $this->sut->getAccessGrantRequest(
            $this->getRequest()
        );
        $this->assertEquals($permission->intent, ContextIntent::CUSTOM);
        $this->assertEquals($permission->name, 'file requests');
    }

    public function testMustAssertPermissionFromRequestMethod()
    {
        $permission = $this->sut->getAccessGrantRequest(
            $this->getRequest()
        );
        $this->assertEquals($permission->intent, ContextIntent::READ);

        $name = "can:" . strtolower(
            ContextIntent::READ->value
        ) . ":" . strtolower('video');

        $this->assertEquals($permission->name, $name);
    }

    public function testShouldThrowWhenNoneRoleAndResource()
    {
        $this->expectException(HttpForbiddenAccessException::class);

        $this->sut->process($this->getRequest(), $this->forgeRequestHandler());
    }

    public function testShouldPassWhenNotAllowedRoleAndResourceButFallbackAvailable()
    {
        $this->accessControl->forgeRole('admin', 'description');
        $this->accessControl->createResourceType('video', 'description');
        $this->sut->setByPassFallback(new class () implements RbacFallbackInterface {
            public function retry(
                Role|string $role,
                ResourceType|string $resource,
                ContextIntent|Permission $permission
            ): bool {
                return true;
            }
        });

        $response = $this->sut->process($this->getRequest(), $this->forgeRequestHandler());

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testShouldThrowWhenRoleFromAccessControlHasNOPermissionToAccess()
    {
        $this->accessControl->createResourceType('video', 'description');
        $this->accessControl->forgeRole('admin', 'description');
        $request = $this->getRequest()->withMethod('POST');

        $this->expectException(HttpForbiddenAccessException::class);

        $this->sut->process($request, $this->forgeRequestHandler());
    }

    public function testShouldThrowWhenRoleFromFetcherHasNOPermissionToAccess()
    {
        $this->accessControl->createResourceType('video', 'description');
        $this->accessControl->forgeRole('admin', 'description');
        $request = $this->getRequest()->withMethod('POST');

        $role = new Role('admin', 'description');
        $resource = new ResourceType('video', '');

        $role->addPermissionToResourceType(
            Permission::makeWithPreferableName(ContextIntent::READ, $resource),
            $resource
        );

        $this->expectException(HttpForbiddenAccessException::class);

        $this->sut->process($request, $this->forgeRequestHandler());
    }

    # Success cases 👇
    public function testShouldPassWhenRoleFromFetcherHasPermissionToAccess()
    {
        $role = new Role('admin', 'description');
        $resource = new ResourceType('video', '');


        $role->addPermissionToResourceType(
            Permission::makeWithPreferableName(ContextIntent::CREATE, $resource),
            $resource
        );

        $this->accessControl->appendRole($role);

        $request = $this->getRequest()->withMethod('POST');

        $response = $this->sut->process($request, $this->forgeRequestHandler());

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $body = $response->getBody()->__toString();

        $this->assertSame($body, 'Success');
    }

    # Success cases 👇
    public function testShouldPassWhenRolePresentInAccessControlHasPermissionToAccess()
    {
        $resource = $this->accessControl->createResourceType('video', 'description');
        $this->accessControl->forgeRole('admin', 'description')->grantAccessOn(
            'admin',
            $resource,
            [Permission::makeWithPreferableName(ContextIntent::CREATE, $resource)]
        );
        $request = $this->getRequest()->withMethod('POST');

        $response = $this->sut->process($request, $this->forgeRequestHandler());

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $body = $response->getBody()->__toString();

        $this->assertSame($body, 'Success');
    }

    private function forgeRequestHandler()
    {

        return new class implements RequestHandler {

            function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $response->getBody()->write('Success');

                return $response;
            }
        };
    }

    private function getRequest()
    {
        return $this->createRequest(
            'GET',
            '/api/test-auth',
            [
                'HTTP_ACCEPT' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        )->withAttribute('token', [
                    'data' => [
                        'email' => 'mail@mail.com',
                        'username' => 'user123',
                        'role' => 'admin',
                        'authType' => 'artchie',
                        'uuid' => null
                    ]
                ]);
    }
}