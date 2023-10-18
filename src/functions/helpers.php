<?php

declare(strict_types=1);

namespace RavineRbac\functions;


/**
 * Provides information about the system's mode
 */
if (!function_exists('mode')) {
    function mode(): string
    {
        return $_ENV['MODE'] ?? '';
    }
}

/**
 * Provides information about the system's mode, whether it is in production mode or not.
 */
if (!function_exists('inTesting')) {
    function inTesting()
    {
        $mode = $_ENV['MODE'] ?? '';
        return $mode === 'TEST';
    }
}




