# RAVINE RBAC

A PHP RBAC implementation using PSR-15, PSR-7 and preconfigured examples using ORMs for running either in long or short-lived processes.

## How does this package work?

In example folder you should have a glimpse of hou this library should be used, but to summarize, you should append an instance of the RBAC Validation Middleware to your PSR-15 stack and attach an array in the attributes of your request object in the following format:

```php

$values = [
    'data' => [
        'email' => 'mail', // optional (future work using accounts with many roles)
        'username' => 'username', // optional (future work using accounts with many roles)
        'role' => 'admin' // -> MANDATORY 
    ]
];

# It could be sent in JSON string format as well

$values = '{"data":{"email":"mail","username":"username","role":"admin"}}';

```

And use the Middleware as:

```php

$accessControl = new AccessControl();
$factory = new RbacValidationFactory($accessControl);

$factory('resource name')->process($request, $handler);

```

Than, the middleware will map the HTTP method to a desired operation (READ, UPDATE, DELETE). You can customize that as well.

This package focuses on being REALLY extensible, which means that you could potentially use it in many other scenarios than above or the ones in `complex-example` directory. You could use the predefined events to store in your database your designated roles (you MUST use ProxyAccessControl for that), implement your own repository layer, extend roles based on your will, and so on. I intentionally provided a repository layer in order to achieve disk storage using Cycle ORM which is more than enough to give you an idea of how to personalize your own repositories layer.

More complex features include

- Event listeners

```php
use RavineRbac/Domain/Events/Events/{
    OnRoleRevokedEvent,
    OnRoleExtendedEvent,
    OnRoleAppendedEvent,
    OnRoleCreateEvent,
    OnResourceCreateEvent,
    OnRbacStart,
    OnResourceAppendedEvent,
    OnAccessAttempt,
    OnPermissionAdded
};

$provider = new ListenerProvider();

$provider->addListener(OnRbacStart::class, fn(OnRbacStart $event) => echo "Make what you want to");

/** @var Middleware */
$middleware = new RoleValidationMiddleware(
    resource: 'image',
    accessControl: new ProxyAccessControl(
        new AccessControl(),
        new EventDispatcher($provider),
        $logger
    )
);

```

- Custom fallbacks

```php

$roleValidationMiddleware->setByPassFallback(new class () implements RbacFallbackInterface {
            public function retry(
                Role|string $role,
                ResourceType|string $resource,
                ContextIntent|Permission $permission
            ): bool {
                return $role->name === 'you know who';
            }
        });
```

- Default Permission Name

```php

$roleValidationMiddleware->setPredefinedPermission(new Permission('file requests', ContextIntent::CUSTOM));

```

- And you

## What is RBAC

Role-based access control (RBAC) refers to the idea of assigning permissions to users based on their role within an organization. It offers a simple, manageable approach to access management that is less prone to error than assigning permissions to users individually.

When using RBAC for Role Management, you analyze the needs of your users and group them into roles based on common responsibilities. You then assign one or more roles to each user and one or more permissions to each role. The user-role and role-permissions relationships make it simple to perform user assignments since users no longer need to be managed individually, but instead have privileges that conform to the permissions assigned to their role(s).

For example, if you were using RBAC to control access for an HR application, you could give HR managers a role that allows them to update employee details, while other employees would be able to view only their own details.

When planning your access control strategy, it's best practice to assign users the fewest number of permissions that allow them to get their work done.

## Rules

All RBAC models must adhere to the following rules:

- Role assignment: a subject can only exercise privileges when the subject is assigned a role.
- Role authorization: the system must authorize a subject’s active role.
- Permission authorization: a subject can only apply permissions granted to the subject’s active role.

## The RBAC Model

There are three types of access control in the RBAC standard: core, hierarchical, and restrictive.
I chose to focus on the first two.

## Domain

A role is a collection of user privileges. Roles are different from traditional groups, which are collections of users. In the context of RBAC, permissions are not directly associated with identities but rather with roles. Roles are more reliable than groups because they are organized around access management. In a typical organization, features and activities change less frequently than identities.

## Idea

A subject (i.e, a person, system, routine) HAS one or more roles. Roles CANNOT be excludent. A subject wants to access a determined resource, but this resource MUST only be accessed under the circunstance of subject owning a set of permissions. A permission MAY have associated intent, such as CREATE, READ, UPDATE or DELETE.

## Refs

<https://frontegg.com/guides/rbac>
<https://auth0.com/docs/manage-users/access-control>

## Inspired by

[PHP Simple RBAC](https://github.com/doganoo/simple-rbac)
[Python Simple RBAC](https://github.com/tonyseek/simple-rbac/tree/master)
[Role, Attribute and conditions based Access Control for Node.js](https://www.npmjs.com/package/role-acl)
