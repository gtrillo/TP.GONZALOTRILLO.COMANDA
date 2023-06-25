<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class VerificarTokenMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');

        try {
            AutentificadorJWT::verificarToken($header);
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
            $response = new ResponseMW();
            $response->getBody()->write($payload);
            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $response;
    }
}


?>