<?php
namespace RavineRbac\Application\Exceptions;

final class HttpForbiddenAccessException extends \Exception
{
    /**
     * @var int
     */
    protected $code = 403;

    /**
     * @var string
     */
    protected $message = 'Forbidden.';

    protected string $title = '403 Forbidden';
    protected string $description = 'You are not allowed to perform the requested operation.';
}
