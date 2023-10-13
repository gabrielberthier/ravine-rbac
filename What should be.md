# What should be

## Ideas

- Proxy AccessControl

```php

$proxyAccessControl = new ProxyAccessControl();


```

## Main flux

```php

$accessControl = AccessControl::get(
    startProvider: fn(): array #array of roles => ... 
);

// User is trying to create a video using their role

$result = $accessControl->tryAccess('role', 'videos', ContextIntent::CREATE);

if($result){
    // Do your thing
}


```
