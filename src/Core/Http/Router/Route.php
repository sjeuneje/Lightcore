<?php

namespace Core\Http\Router;

use Core\Http\Request;
use Exception;

/**
 * Represents a single route in the routing system.
 *
 * Handles route matching, parameter extraction, and callback execution
 * for HTTP requests with support for dynamic URL parameters.
 */
class Route
{
    /**
     * The HTTP method for this route (GET, POST, etc.)
     */
    private string $method;

    /**
     * The URL path pattern with optional parameters like /users/{id}
     */
    private string $path;

    /**
     * The callback to execute when route matches (closure or [Controller, method])
     */
    private $callback;

    /**
     * Base path to remove from request URI (configurable)
     */
    private static string $basePath = '/lightcore';

    /**
     * Create a new route instance.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param string $path URL path pattern (e.g., 'api/users/{id}')
     * @param callable|array $callback Controller method or closure to execute
     */
    public function __construct(string $method, string $path, $callback)
    {
        $this->method = strtoupper($method); // Standardizing HTTP method
        $this->path = $path;
        $this->callback = $callback;
    }

    /**
     * Check if this route matches the given request.
     *
     * @param Request $request The HTTP request to match against
     * @return bool True if route matches, false otherwise
     */
    public function matches(Request $request): bool
    {
        return $request->getMethod() === $this->method &&
            preg_match($this->buildRegexPattern(), $this->cleanUri($request));
    }

    /**
     * Extract URL parameters from the request if route matches.
     *
     * @param Request $request The HTTP request to extract parameters from
     * @return array Array of parameter values in order of appearance
     */
    public function getParameters(Request $request): array
    {
        if (!$this->matches($request)) {
            return [];
        }

        preg_match($this->buildRegexPattern(), $this->cleanUri($request), $matches);
        array_shift($matches); // Remove full match, keep only capture groups

        // Extract parameter names from the route
        $paramNames = $this->parameterNames();
        return array_combine($paramNames, $matches);
    }

    /**
     * Get parameter names from the route path.
     *
     * @return array An array of parameter names
     */
    private function parameterNames(): array
    {
        preg_match_all('/\{(\w+)\}/', $this->path, $matches);
        return $matches[1];
    }

    /**
     * Execute the route callback with the given request.
     *
     * @param Request $request The HTTP request object
     * @return mixed The result of the callback execution
     * @throws Exception If callback is invalid
     */
    public function handle(Request $request): mixed
    {
        $params = $this->getParameters($request);
        $request->setParams($params);

        if (is_array($this->callback)) {
            $controller = new $this->callback[0]();
            $method = $this->callback[1];

            return call_user_func([$controller, $method], $request);
        } elseif (is_callable($this->callback)) {
            return call_user_func($this->callback, $request, ...$params);
        }

        throw new Exception("Invalid callback provided");
    }

    /**
     * Set the base path to remove from request URIs.
     *
     * @param string $basePath The base path to remove
     */
    public static function setBasePath(string $basePath): void
    {
        self::$basePath = $basePath;
    }

    /**
     * Clean and normalize the request URI for matching.
     *
     * @param Request $request The HTTP request
     * @return string The cleaned URI path
     */
    private function cleanUri(Request $request): string
    {
        $path = parse_url($request->getUri(), PHP_URL_PATH);
        $cleaned = str_replace(self::$basePath, '', $path);
        return ltrim($cleaned, '/') ?: '/';
    }

    /**
     * Build a regex pattern from the route path for matching.
     *
     * Converts patterns like 'api/users/{id}' into regex patterns
     * like '/^api\/users\/([^\/]+)$/' for URL matching.
     *
     * @return string The regex pattern with delimiters
     */
    private function buildRegexPattern(): string
    {
        // Escape forward slashes for regex
        $pattern = str_replace('/', '\/', $this->path);

        // Replace {param} with capture groups
        $pattern = preg_replace('/\{[^}]+\}/', '([^\/]+)', $pattern);

        return '/^' . $pattern . '$/';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
