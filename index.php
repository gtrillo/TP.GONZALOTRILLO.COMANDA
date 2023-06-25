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
require_once 'app/models/usuario.php';
require_once 'app/controller/MesaController.php';
require_once 'app/middleware/jwt.php';
require_once 'app/middleware/authentificador.php';
require __DIR__ . '/vendor/autoload.php';
// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('/login', \UsuarioController::class . ':Login');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(function (Request $request, RequestHandler $handler) {
        $parametros = $request->getParsedBody();
        $usuario = $parametros['usuario'];
        $rol = $parametros['rol'];
        $alias = $parametros['alias'];
        $datos = array('usuario' => $usuario, 'rol' => $rol, 'alias' => $alias);

        $token = AutentificadorJWT::CrearToken($datos);

        $response = $handler->handle($request);
        $payload = json_encode(array('jwt' => $token));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });
});

session_start();
$app->group('/producto', function (RouteCollectorProxy $group) {
    $group->post('[/]', \ProductoController::class . ':CargarUno');
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{id}', \ProductoController::class . ':TraerUno');
    $group->put('/modificar/{id}', \ProductoController::class . ':ModificarUno');
})->add(new VerificarTokenMiddleware());

$app->group('/pedido', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/{codigo}', \PedidoController::class . ':TraerUno');
    $group->post('[/]', \PedidoController::class . ':cargarUno');
    $group->put('/modificar/{id}', \PedidoController::class . ':ModificarUno');
})->add(new VerificarTokenMiddleware());

$app->group('/mesa', function (RouteCollectorProxy $group) {
    $group->post('[/]', \MesaController::class . ':CargarUno');
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{codigo}', \MesaController::class . ':TraerUno');
    $group->put('/modificar/{id}', \MesaController::class . ':ModificarUno');
})->add(new VerificarTokenMiddleware());



$app->run();
?>