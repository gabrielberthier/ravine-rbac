<?php

declare(strict_types=1);

namespace Tests\Domain\Rbac;

use RavineRbac\Domain\Models\Account;
use RavineRbac\Domain\Models\AccessControl;
use RavineRbac\Domain\Models\ContextIntent;
use RavineRbac\Domain\Models\Permission;
use RavineRbac\Domain\Models\ResourceType;
use RavineRbac\Domain\Models\Role;
use PHPUnit\Framework\TestCase;


class ExecutorTest extends TestCase
{
    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account(
            access: 'username'
        );
    }

    public function testShouldReturnTrueForAccessableResource()
    {
        $role = new Role('image_role', '');
        $resource = new ResourceType('image', 'images resources');
        $canCreate = new Permission('can:create', ContextIntent::READ);
        $role->addPermissionToResourceType($canCreate, $resource);
        $this->account->addRole($role);

        $this->assertTrue($this->account->canAccess($resource, ContextIntent::READ));
    }

    public function testShouldReturnFalseForInaccessableResource()
    {
        $role = new Role('image_role', '');
        $resource = new ResourceType('image', 'images resources');
        $canCreate = new Permission('can:create', ContextIntent::READ);
        $role->addPermissionToResourceType($canCreate, $resource);
        $this->account->addRole($role);

        $this->assertFalse($this->account->canAccess($resource, ContextIntent::CREATE));
    }

    public function testAccessControlEmitsStringObject()
    {
        $accessControl = new AccessControl();
        $accessControl->createResourceType('image', 'images resources');
        $role = $accessControl
            ->forgeRole('image:role')
            ->addPermissionToRole('image:role', 'image', ContextIntent::CREATE)
            ->getRole("image:role")->get();

        $this->assertStringContainsStringIgnoringCase(
            json_encode($role->jsonSerialize()),
            $accessControl->toJson()
        );
    }

    public function testAccessControlWillAllowPass()
    {
        $accessControl = new AccessControl();
        $resource = new ResourceType('image', 'images resources');
        $accessControl->appendResourceType($resource);
        $accessControl
            ->forgeRole('image:role')
            ->addPermissionToRole(
                'image:role',
                $resource,
                ContextIntent::CREATE
            );

        $this->assertTrue(
            $accessControl->tryAccess(
                'image:role',
                'image',
                ContextIntent::CREATE
            )
        );
    }

    public function testAccessControlWillNotAllowPassForDifferentIntent()
    {
        $accessControl = new AccessControl();
        $resource = new ResourceType('image', 'images resources');
        $accessControl->appendResourceType($resource);
        $accessControl
            ->forgeRole('image:role')
            ->addPermissionToRole(
                'image:role',
                $resource,
                ContextIntent::READ
            );

        $this->assertFalse(
            $accessControl->tryAccess(
                'image:role',
                'image',
                ContextIntent::CREATE
            )
        );
    }

    public function testAccessControlWillAllowPassForDifferentIntentButTruethyFallback()
    {
        $accessControl = new AccessControl();
        $resource = new ResourceType('image', 'images resources');
        $accessControl->appendResourceType($resource);
        $accessControl
            ->forgeRole('image:role')
            ->addPermissionToRole(
                'image:role',
                $resource,
                ContextIntent::READ
            );

        $this->assertTrue(
            $accessControl->tryAccess(
                'image:role',
                'image',
                ContextIntent::CREATE,
                static function ($role, $resource, $permission) {
                    return true;
                }
            )
        );
    }

    public function testAccessControlWillNotAllowPassForDifferentIntentAndFalsyFallback()
    {
        $accessControl = new AccessControl();
        $resource = new ResourceType('image', 'images resources');
        $accessControl->createResourceType('image', 'images resources');
        $accessControl
            ->forgeRole('image:role')
            ->addPermissionToRole(
                'image:role',
                $resource,
                ContextIntent::READ
            );

        $this->assertFalse(
            $accessControl->tryAccess(
                'image:role',
                'image',
                ContextIntent::CREATE,
                static function ($role, $resource, $permission) {
                    return false;
                }
            )
        );
    }

}