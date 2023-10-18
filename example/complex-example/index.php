<?php
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\Database\DatabaseManager;
use HttpSoft\Message\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RavineRbac\Application\Middleware\RoleValidationMiddleware;
use RavineRbac\Data\Proxy\ProxyAccessControl;
use RavineRbac\Domain\Events\EventDispatcher;
use RavineRbac\Domain\Models\RBAC\AccessControl;
use RavineRbac\Domain\Models\RBAC\ContextIntent;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;
use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Static\CycleOrmFactory;
use RavineRbac\Infra\Persistence\Cycle\RbacDb\CycleRoleAccessCreator;
use Spiral\Core\Container;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use HttpSoft\ServerRequest\ServerRequestCreator;

require __DIR__ . '/../../bootstrap.php';

$databaseManager = new DatabaseManager(
    new DatabaseConfig([
        "default" => "default",
        "databases" => [
            "default" => [
                "connection" => "sqlite_file",
            ],
        ],
        "connections" => [
            'sqlite_file' => new SQLiteDriverConfig(
                connection: new FileConnectionConfig(
                    database: './database.sqlite'
                ),
                queryCache: true,
            ),
        ],
    ])
);

$orm = CycleOrmFactory::createOrm($databaseManager, new Container());

$accessCreator = new CycleRoleAccessCreator($orm);
$roleObject = new Role("resource_owner", "Resource Owner Role");
$resource = new ResourceType('image', 'images resources');
$canCreate = Permission::makeWithPreferableName(ContextIntent::READ, $resource);

$accessCreator->create(
    $roleObject,
    $resource,
    $canCreate
);

$providerFactory = require __DIR__ . '/listeners.php';

$providers = $providerFactory($orm);
$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('./your.log'));

/** @var Middleware */
$middleware = new RoleValidationMiddleware(
    resource: 'image',
    accessControl: new ProxyAccessControl(
        new AccessControl(),
        new EventDispatcher($providers),
        $logger
    )
);

$request = ServerRequestCreator::create();
$request = $request->withAttribute('token', [
    'data' => [
        'email' => 'mail',
        'username' => 'username',
        'role' => 'resource_owner'
    ]
]);

// Will work!

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