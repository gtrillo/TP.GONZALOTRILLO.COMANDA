<?php
require_once 'app/models/encuesta.php';
require_once 'app/interfaces/IApiUsable.php';

class EncuestaController extends Encuesta //implements IApiUsable
  {
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombreCliente=$parametros['nombreCliente'];
        $numeroPedido=$parametros['numeroPedido'];
        $puntuacionMesa=$parametros['puntuacionMesa'];
        $puntuacionRestaurante=$parametros['puntuacionRestaurante'];
        $puntuacionMozo=$parametros['puntuacionMozo'];
        $puntuacionCocinero=$parametros['puntuacionCocinero'];
        $textoExperiencia=$parametros['textoExperiencia'];

        $encuesta = new Encuesta();
        $encuesta->nombreCliente = $nombreCliente;
        $encuesta->numeroPedido = $numeroPedido;
        $encuesta->puntuacionMesa = $puntuacionMesa;
        $encuesta->puntuacionRestaurante = $puntuacionRestaurante;
        $encuesta->puntuacionMozo = $puntuacionMozo;
        $encuesta->puntuacionCocinero = $puntuacionCocinero;
        $encuesta->textoExperiencia = $textoExperiencia;

        $encuesta->crearEncuesta();
    
        $payload = json_encode(array("mensaje" => "Encuesta enviada con exito, muchas gracias!"));
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*

  
    public function TraerUno($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $mesa = Mesa::obtenerMesa($codigo);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $cantidad = $parametros['cantidad'];
        $id = $parametros['id'];

        Mesa::modificarMesa($nombre, $precio, $cantidad,$id);

        $payload = json_encode(array("mensaje" => "Producto modificado con éxito"));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesaId = $parametros['mesaId'];
        Mesa::borarMesa($mesaId);
        $payload = json_encode(array("mensaje" => "Mesa borrado con exito"));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

   */
}
?>