<?php

namespace Tests\Infrastructure\Persistence\Orm\Rbac;

use Core\Data\Factories\CycleDatabaseManagerFactory;
use Core\Data\Factories\CycleOrmFactory;
use RavineRbac\Data\Entities\Cycle\Rbac\CyclePermission;
use RavineRbac\Data\Entities\Cycle\Rbac\CycleResource;
use RavineRbac\Data\Entities\Cycle\Rbac\CycleRole;
use RavineRbac\Domain\Models\RBAC\ContextIntent;
use RavineRbac\Domain\Models\RBAC\Permission;
use RavineRbac\Domain\Models\RBAC\ResourceType;
use RavineRbac\Domain\Models\RBAC\Role;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use RavineRbac\Infra\Persistence\Cycle\RbacDb\CycleRoleAccessCreator;
use RavineRbac\Infra\Persistence\Cycle\RbacDb\CycleRoleAccessRepository;
use RavineRbac\Infra\Persistence\Cycle\RbacDb\CycleRoleExtender;
use Spiral\Core\Container;
use Tests\TestCase;

#[CoversNothing]
#[Group('cycleorm')]
final class CycleRoleAccessTest extends TestCase
{
    private CycleRoleAccessCreator $sut;
    private EntityManager $em;
    private ORM $orm;

    public static function setUpBeforeClass(): void
    {
        self::createDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        self::truncateDatabase();
    }

    public function setUp(): void
    {
        $this->orm = CycleOrmFactory::createOrm(
            CycleDatabaseManagerFactory::createDatabase(),
            new Container()
        );
        $this->em = new EntityManager($this->orm);
        $this->sut = new CycleRoleAccessCreator($this->orm);
    }

    protected function tearDown(): void
    {
        $collection = $this->orm->getRepository(CycleRole::class)->findAll();
        foreach ($collection as $c) {
            $this->em->delete($c, cascade: true);
        }
        $collection = $this->orm->getRepository(CycleResource::class)->findAll();
        foreach ($collection as $c) {
            $this->em->delete($c, cascade: true);
        }
        $collection = $this->orm->getRepository(CyclePermission::class)->findAll();
        foreach ($collection as $c) {
            $this->em->delete($c);
        }
        $this->em->run();
    }

    public function testShouldInsertRole()
    {
        $roleObject = new Role("resource_owner", "Resource Owner Role");
        $resource = new ResourceType('image', 'images resources');
        $canCreate = Permission::makeWithPreferableName(ContextIntent::READ, $resource);
        $role = $this->sut->create(
            $roleObject,
            $resource,
            $canCreate
        );
        $this->assertNotNull($role);
        $this->assertTrue($role->isOk());
    }

    public function testShouldGetLastInsertedRoleByName()
    {
        $roleObject = new Role("resource_owner", "Resource Owner Role");
        $resource = new ResourceType('image', 'images resources');
        $canCreate = Permission::makeWithPreferableName(ContextIntent::READ, $resource);
        $this->sut->create(
            $roleObject,
            $resource,
            $canCreate
        );

        $repo = new CycleRoleAccessRepository($this->orm);
        $retrievedCycleRole = $repo
            ->getRoleWithPermissions($roleObject, $resource)
            ->unwrap();

        $this->assertEquals($retrievedCycleRole->getName(), $roleObject->name);
        $this->assertCount(1, $retrievedCycleRole->permissions->toArray());
    }

    public function testShouldGetLastInsertedRoleByNameWithExtendedRole()
    {
        $roleObject = new Role("resource_owner", "Resource Owner Role");
        $roleObject2 = new Role("resource_owner2", "Child Resource Owner Role");

        $resource = new ResourceType('image', 'images resources');
        $canCreate = Permission::makeWithPreferableName(ContextIntent::READ, $resource);

        $this->sut->create(
            $roleObject,
            $resource,
            $canCreate
        );

        $extender = new CycleRoleExtender($this->orm);
        $extender->extend($roleObject2, $roleObject);

        /** @var ?CycleRole $cycleChildRole */
        $cycleChildRole = $this->orm
            ->getRepository(CycleRole::class)
            ->select()
            ->where('name', $roleObject2->name)
            ->fetchOne();

        $this->assertSame(1, $cycleChildRole->extendedRoles->count());
        $this->assertEquals("resource_owner", $cycleChildRole->extendedRoles->first()->name);
    }
}