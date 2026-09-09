<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../core/Router.php';


$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim($scriptName, '/');

if ($basePath === '.') {
    $basePath = '';
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!empty($basePath) && strpos($uri, $basePath) === 0) {
    $relativeUri = substr($uri, strlen($basePath));
} else {
    $relativeUri = $uri;
}

$relativeUri = '/' . ltrim($relativeUri, '/');


// API V1
if (
    $relativeUri === '/api/v1' ||
    str_starts_with($relativeUri, '/api/v1/')
) {

    require_once '../resources/v1/UserResource.php';
    require_once '../resources/v1/ProductResource.php';

    $router = new Router('v1', $basePath);

    $userResource = new UserResource();
    $productResource = new ProductResource();

    // Usuarios
    $router->addRoute('GET', '/users', [$userResource, 'index']);
    $router->addRoute('GET', '/users/{id}', [$userResource, 'show']);
    $router->addRoute('POST', '/users', [$userResource, 'store']);
    $router->addRoute('PUT', '/users/{id}', [$userResource, 'update']);
    $router->addRoute('DELETE', '/users/{id}', [$userResource, 'destroy']);

    // Productos
    $router->addRoute('GET', '/productos', [$productResource, 'index']);
    $router->addRoute('GET', '/productos/{id}', [$productResource, 'show']);
    $router->addRoute('POST', '/productos', [$productResource, 'store']);
    $router->addRoute('PUT', '/productos/{id}', [$productResource, 'update']);
    $router->addRoute('DELETE', '/productos/{id}', [$productResource, 'destroy']);

    $router->dispatch();
    exit;
}


// API V2

if (
    $relativeUri === '/api/v2' ||
    str_starts_with($relativeUri, '/api/v2/')
) {

    require_once '../resources/v2/UserResource.php';
    require_once '../resources/v2/ProductResource.php';
    require_once '../resources/v2/AuthResource.php';

    $router = new Router('v2', $basePath);

    $userResource = new UserResource();
    $productResource = new ProductResource();
    $authResource = new AuthResource();

    $router->addRoute('POST', '/login', [$authResource, 'login']);
    $router->addRoute('GET', '/me', [$authResource, 'me']);
    $router->addRoute('POST', '/logout', [$authResource, 'logout']);

    $protected = function ($handler) use ($authResource) {

    return function (...$args) use ($authResource, $handler) {

        $user = $authResource->authenticate();

        if (!$user) {
            return;
        }

        return call_user_func_array($handler, $args);
    };
};
 
$router->addRoute('GET', '/users',
$protected([$userResource, 'index'])
);

$router->addRoute('GET', '/users/{id}',
    $protected([$userResource, 'show'])
);

$router->addRoute('POST', '/users',
    $protected([$userResource, 'store'])
);

$router->addRoute('PUT', '/users/{id}',
    $protected([$userResource, 'update'])
);

$router->addRoute('DELETE', '/users/{id}',
    $protected([$userResource, 'destroy'])
);


$router->addRoute('GET', '/productos',
    $protected([$productResource, 'index'])
);

$router->addRoute('GET', '/productos/{id}',
    $protected([$productResource, 'show'])
);

$router->addRoute('POST', '/productos',
    $protected([$productResource, 'store'])
);

$router->addRoute('PUT', '/productos/{id}',
    $protected([$productResource, 'update'])
);

$router->addRoute('DELETE', '/productos/{id}',
    $protected([$productResource, 'destroy'])
);

   
    $router->dispatch();
    exit;
}

// API V3
if (
    $relativeUri === '/api/v3' ||
    str_starts_with($relativeUri, '/api/v3/')
) {

    require_once '../resources/v3/TaskResource.php';

    $router = new Router('v3', $basePath);

    $taskResource = new TaskResource();

    $router->addRoute('GET', '/tareas', [$taskResource, 'index']);
    $router->addRoute('GET', '/tareas/{id}', [$taskResource, 'show']);
    $router->addRoute('POST', '/tareas', [$taskResource, 'store']);
    $router->addRoute('PUT', '/tareas/{id}', [$taskResource, 'update']);

    $router->dispatch();
    exit;
}


// Versión inexistente
header("Content-Type: application/json");
http_response_code(404);

echo json_encode([
    "message" => "Versión de API no encontrada"
]);

?>
