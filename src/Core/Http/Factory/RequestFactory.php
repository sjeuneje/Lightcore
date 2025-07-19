<?php

namespace Core\Http\Factory;

use Core\Http\Request;

/**
 * Simple factory for creating Request instances
 *
 * Provides convenient static methods for creating Request objects
 * from current environment or for testing purposes.
 */
class RequestFactory
{
    /**
     * Create Request from current environment
     *
     * @return Request Current HTTP request
     */
    public static function createFromGlobals(): Request
    {
        return new Request($_SERVER);
    }

    /**
     * Create Request for testing with custom data
     *
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @param array<string, mixed> $parameters Request parameters
     * @param array<string, string> $headers Request headers
     * @return Request Mock request for testing
     */
    public static function create(
        string $method = 'GET',
        string $uri = '/',
        array $parameters = [],
        array $headers = []
    ): Request {
        $server = [
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $uri,
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => '80',
            'HTTPS' => 'off'
        ];

        foreach ($headers as $name => $value) {
            $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
            $server[$key] = $value;
        }

        $_GET = $method === 'GET' ? $parameters : [];
        $_POST = $method === 'POST' ? $parameters : [];

        return new Request($server);
    }
}
