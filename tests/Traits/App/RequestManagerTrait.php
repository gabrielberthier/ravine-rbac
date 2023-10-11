<?php

namespace Tests\Traits\App;

use Psr\Http\Message\ServerRequestInterface;
use HttpSoft\ServerRequest\ServerRequestCreator;

trait RequestManagerTrait
{
    protected function createRequest(
        string $method,
        string $path,
        array $headers = [
            'HTTP_ACCEPT' => 'application/json',
            'Content-Type' => 'application/json',
        ],
        array $serverParams = [],
        array $cookies = []
    ): ServerRequestInterface {
        $request = ServerRequestCreator::create();

        foreach ($headers as $name => $value) {
            $request->withAddedHeader($name, $value);
        }

        return $request;
    }
}