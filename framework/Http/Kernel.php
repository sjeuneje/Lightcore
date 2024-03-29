<?php

namespace Lightcore\Framework\Http;

use FastRoute\RouteCollector;

use Lightcore\Framework\View\View;
use function FastRoute\simpleDispatcher;

class Kernel
{
    public function handle(Request $request): Response|View
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $routeCollector) {

            $routes = include BASE_PATH . '/routes/web.php';

            foreach ($routes as $route) {
                $route[1] = rtrim($_SERVER['REQUEST_URI'], '/') . $route[1];
                $routeCollector->addRoute(...$route);
            }
        });

        $routeInfo = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getPathInfo()
        );

        [$status, [$controller, $method], $vars] = $routeInfo;

        return call_user_func_array([new $controller, $method], $vars);
    }
}