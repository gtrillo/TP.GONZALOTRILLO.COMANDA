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
        $queryParams = $request->getQueryParams();
        $codigoPedido = $queryParams['codigoPedido'];
        $pedido = Pedido::obtenerPedido($codigoPedido);
        $payload = json_encode($pedido);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Cobrar($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
    
        $numeroPedido = $parametros['numeroPedido'];
        $codigoMesa = $parametros['codigoMesa'];
    
        $cuenta = Pedido::CobrarCuenta($codigoMesa, $numeroPedido);
        $payload = json_encode(array("cuenta" => $cuenta));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerResueltosATiempo($request, $response, $args)
    {
        $lista = Pedido::obtenerPedidosResueltosATiempo();
        $payload = json_encode(array("listaPedidos" => $lista));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function TraerPedidosListos($request, $response, $args)
    {
        $lista = Pedido::ObternerPedidosListos();
        $payload = json_encode(array("listaPedidos" => $lista));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $estado = $parametros['estado'];
        $tiempoDeResolucion = $parametros['tiempoDeResolucion'];


        Pedido::modificarPedidoEnPreparacion($id, $estado, $tiempoDeResolucion);

        $payload = json_encode(array("mensaje" => "Pedido modificado con éxito"));

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
