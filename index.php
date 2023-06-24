<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as ResponseMW;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once 'app/controller/UsuarioController.php';
require_once 'app/controller/PedidoController.php';
require_once 'app/controller/ProductoController.php';
require_once 'app/controller/MesaController.php';

require __DIR__ . '/vendor/autoload.php';
// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->group('/JSON', function (RouteCollectorProxy $group) {
    $group->map(['GET', 'POST'], '', function ($request, $response, array $args) {
        $data = array("API" => "GET o POST");
        $response->getBody()->write(json_encode($data));
        return $response;
    });
})->add(function (Request $request, RequestHandler $handler) {
    $respuesta = "";

    if ($request->getMethod() === "GET") {
        $respuesta = json_encode(array("API" => "GET", "status" => 200));
        $response = new ResponseMW(200);
        $response->getBody()->write($respuesta);
        return $response;
    }else if($request->getMethod() === "POST"){
        echo "wadawd";
    }

});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  });

  $app->group('/producto', function (RouteCollectorProxy $group){
    $group->post('[/]', \ProductoController::class . ':CargarUno');
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{id}', \ProductoController::class . ':TraerUno');
    $group->put('/modificar/{id}', \ProductoController::class . ':ModificarUno');
});

$app->group('/pedido', function (RouteCollectorProxy $group){
    $group->post('[/]', \PedidoController::class . ':CargarUno');
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/{codigo}', \PedidoController::class . ':TraerUno');
    $group->put('/modificar/{id}', \PedidoController::class . ':ModificarUno');
});



$app->group('/mesa', function (RouteCollectorProxy $group){
    $group->post('[/]', \MesaController::class . ':CargarUno');
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{codigo}', \MesaController::class . ':TraerUno');
    $group->put('/modificar/{id}', \MesaController::class . ':ModificarUno');
});




$app->run();
