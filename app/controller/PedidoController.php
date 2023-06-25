<?php
require_once 'app/models/Pedido.php';
require_once 'app/models/Producto.php';
require_once 'app/models/Usuario.php';
require_once 'app/interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $estado = $parametros['estado'];
        $foto = $parametros['foto'];
        $numeroPedido = $parametros['numeroPedido'];
        $tiempoDeResolucion = $parametros['tiempoDeResolucion'];
        $listaProductosEntrada = $parametros['listaProductos'];
        $codigoMesa = $parametros['codigoMesa'];
        $listaProductosFinal = array();

        foreach ($listaProductosEntrada as $item) {
            $producto = Producto::obtenerProductoXNombre($item['nombre']);
            if ($producto == null) {
                echo "Producto " . $item['nombre'] . " no encontrado";
            } else {
                $listaProductosFinal[] = $producto;
            }
        }
        
        $pedido = new Pedido();
        $pedido->estado = $estado;
        $pedido->foto = $foto;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->codigoPedido = $numeroPedido;
        $pedido->tiempoDeResolucion = $tiempoDeResolucion;
        $pedido->listaProductos = $listaProductosFinal;
        $pedido->CrearPedido();
    
        $payload = json_encode(array("mensaje" => "Pedido creado con éxito"));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $codigoPedido = $args['codigoPedido'];
        $pedido = Pedido::obtenerPedido($codigoPedido);
        $payload = json_encode($pedido);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $cantidad = $parametros['cantidad'];
        $id = $parametros['id'];

        Producto::modificarProducto($nombre, $precio, $cantidad,$id);

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
