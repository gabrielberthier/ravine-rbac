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




```
