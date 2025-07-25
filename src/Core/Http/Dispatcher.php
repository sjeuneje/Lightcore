<?php

namespace Core\Http;

use Core\Exceptions\HttpException;

class Dispatcher {
    /**
     * Dispatch the request to the appropriate handler.
     *
     * @param Request $request
     * @throws HttpException
     * @return mixed
     */
    public static function dispatch(Request $request): mixed
    {
        $route = Router::match($request);
        if (!$route) {
            throw new HttpException("Route not found", 404);
        }

        return $route->handle($request);
    }
}
