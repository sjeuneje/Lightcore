<?php

use Core\Autoloader;
use Core\Container;
use Core\Http\Factory\RequestFactory;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router\Route;
use App\Controllers\UserController;

require_once "../src/helpers.php";
require_once "../src/Core/Autoloader.php";
require_once "../app/Controllers/UserController.php";

Autoloader::register();

$container = new Container();

$container->singleton(Request::class, function() {
    return RequestFactory::createFromGlobals();
});

$container->singleton(Response::class, function() {
    return new Response();
});

$request = $container->get(Request::class);

// Test different routes
$routes = [
    new Route('GET', 'api/users', [UserController::class, 'index']),
    new Route('GET', 'api/users/{id}', [UserController::class, 'show']),
    new Route('POST', 'api/users', function(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer'
        ]);

        $user = [
            'id' => 3,
            'name' => $request->post('name'),
            'age' => $request->post('age')
        ];
        $data = [
          'message' => 'User ' . $user['name'] . '#' . $user['id'] . ' created.',
          'data' => $user
        ];

        return Response::html(json_encode($data))->send();
    })
];

echo "<h2>🧪 Route Testing</h2>";
echo "<p><strong>Request:</strong> {$request->getMethod()} {$request->getUri()}</p>";

foreach ($routes as $route) {
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
    echo "<h3>Route: {$request->getMethod()} /{$route->getPath()}</h3>";

    if ($route->matches($request)) {
        echo "✅ <strong>MATCH!</strong><br>";
        $params = $route->getParameters($request);
        if (!empty($params)) {
            echo "📋 Parameters: " . json_encode($params) . "<br>";
        }
        try {
            echo "🚀 Executing route...<br>";
            $route->handle($request);
            echo "<br>";
            echo "✨ Route would execute successfully!";
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    } else {
        echo "❌ No match";
    }
    echo "</div>";
}
?>
