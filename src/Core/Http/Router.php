<?php

namespace Core\Http;

use Core\Exceptions\HttpException;
use Core\Http\Router\Route;

class Router {
    /**
     * Array containing all the registered routes.
     *
     * @var Route[]
     */
    private static array $routes = [];

    /**
     * Registers a GET route.
     *
     * @param string $path The path for the route.
     * @param callable $handler The handler for the route.
     * @return void
     */
    public static function get(string $path, callable $handler): void
    {
        self::addRoute('GET', $path, $handler);
    }

    /**
     * Registers a POST route.
     *
     * @param string $path The path for the route.
     * @param callable $handler The handler for the route.
     * @return void
     */
    public static function post(string $path, callable $handler): void
    {
        self::addRoute('POST', $path, $handler);
    }

    /**
     * Registers a PATCH route.
     *
     * @param string $path The path for the route.
     * @param callable $handler The handler for the route.
     * @return void
     */
    public static function patch(string $path, callable $handler): void
    {
        self::addRoute('PATCH', $path, $handler);
    }

    /**
     * Registers a DELETE route.
     *
     * @param string $path The path for the route.
     * @param callable $handler The handler for the route.
     * @return void
     */
    public static function delete(string $path, callable $handler): void
    {
        self::addRoute('DELETE', $path, $handler);
    }

    /**
     * Adds a route to the router.
     *
     * @param string $method The HTTP method for the route.
     * @param string $path The path for the route.
     * @param callable $handler The handler for the route.
     * @return void
     */
    private static function addRoute(string $method, string $path, callable $handler): void
    {
        self::$routes[] = new Route($method, $path, $handler);
    }

    /**
     * Matches a request to a registered route.
     *
     * @param Request $request The incoming request.
     * @return Route The matching route.
     * @throws HttpException If no matching route is found.
     */
    public static function match(Request $request): Route
    {
        foreach (self::$routes as $route) {
            if ($route->matches($request)) {
                return $route;
            }
        }

        throw new HttpException("Error: Route '{$request->getUri()}' not found.", Response::HTTP_NOT_FOUND);
    }

    /**
     * Dispatches the request to the matching route handler.
     *
     * @param Request $request The incoming request.
     * @return mixed The result of the route handler.
     * @throws HttpException If no matching route is found.
     */
    public static function dispatch(Request $request): mixed
    {
        $route = self::match($request);
        return $route->handle($request);
    }
}
