<?php
require_once 'app/models/mesa.php';
require_once 'app/interfaces/IApiUsable.php';

class MesaController extends Usuario //implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];


        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->rol = $rol;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $mesa = Mesa::obtenerMesa($codigo);
        $payload = json_encode($usuario);

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

   
}
