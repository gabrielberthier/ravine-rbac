<?php

use HttpSoft\Message\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RavineRbac\Application\Middleware\RoleValidationMiddleware;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\ContextIntent;
use RavineRbac\Domain\Models\RBAC\Role;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use HttpSoft\ServerRequest\ServerRequestCreator;

require __DIR__ . '/../../bootstrap.php';

$accessControl = new AccessControl();

$role = new Role('admin', 'Admin Privileges');

// This is the same as below, but preferable since it holds reference to resource outside Role's WeakMap.
// In case you actually want to use instances, please append them in access control interface.

$accessControl->forgeRole('admin', 'Admin Privileges')
    ->createResourceType('image', 'Image Resources')
    ->addPermissionToRole('admin', 'image', ContextIntent::READ);

// $role->addPermissionToResourceType(
//     new Permission('read', ContextIntent::READ),
//     new ResourceType('image', 'Image Resources')
// );

/** @var Middleware */
$middleware = new RoleValidationMiddleware(
    resource: 'image',
    accessControl: $accessControl
);

$request = ServerRequestCreator::create();
$request = $request->withAttribute('token', [
    'data' => [
        'email' => 'mail',
        'username' => 'username',
        'role' => 'admin'
    ]
]);

// This will work!

$middleware->process($request, new class implements RequestHandlerInterface {

    function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write('Success');

        echo 'User is allowed to access' . PHP_EOL;

        return $response;
    }
});

// And this WILL NOT!
// Expect <HttpForbiddenAccessException>

$request = ServerRequestCreator::create();
$request = $request->withAttribute('token', [
    'data' => [
        'email' => 'mail',
        'username' => 'username',
        'role' => 'anyotherrole'
    ]
]);

$middleware->process($request, new class implements RequestHandlerInterface {

    function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write('Success');

        echo 'User is allowed to access' . PHP_EOL;

        return $response;
    }
});