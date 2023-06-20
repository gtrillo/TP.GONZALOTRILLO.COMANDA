<?php
require_once 'app/models/Producto.php';
require_once 'app/interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $precio = $parametros['precio'];
        $nombre = $parametros['nombre'];
        $cantidad = $parametros['cantidad'];

        // Creamos el producto
        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->cantidad = $cantidad;
        $producto->precio = $precio;
        $producto->CrearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con éxito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $productoId = $args['id'];
        $producto = Producto::obtenerProducto($productoId);
        $payload = json_encode($producto);
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function CrearUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $nombre = $parametros['nombre'];
        $cantidad = $parametros['cantidad'];
        $precio = $parametros['precio'];
    
        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->cantidad = $cantidad;
        $producto->precio = $precio;
        $producto->crearProducto();
    
        $payload = json_encode(array("mensaje" => "Producto creado con éxito"));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    /*
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $productoId = $args['id'];
        $nombre = $parametros['nombre'];
        $cantidad = $parametros['cantidad'];
        $precio = $parametros['precio'];
    
        $producto = new Producto();
        $producto->id = $productoId;
        $producto->nombre = $nombre;
        $producto->cantidad = $cantidad;
        $producto->precio = $precio;
        $producto->actualizarProducto();
    
        $payload = json_encode(array("mensaje" => "Producto actualizado con éxito"));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }*/


    
    public function ModificarUno($request, $response, $args)
{
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $precio = $parametros['precio'];
    $cantidad = $parametros['cantidad'];
   

    Producto::modificarProducto($nombre, $precio, $cantidad);

    $payload = json_encode(array("mensaje" => "Producto modificado con éxito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
}
    
    public function BorrarUno($request, $response, $args)
    {
        $productoId = $args['id'];
        $producto = new Producto();
        $producto->id = $productoId;
        $producto->eliminarProducto();
    
        $payload = json_encode(array("mensaje" => "Producto borrado con éxito"));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
   
}
