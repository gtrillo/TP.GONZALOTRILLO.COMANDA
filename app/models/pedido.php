<?php
require_once 'app/models/mesa.php';
require_once 'app/models/usuario.php';
class Pedido {

    public $id;
    public $codigoPedido;
    public $listaProductos;
    public $estado;
    public $tiempoDeResolucion;
    public $codigoMesa;

    public function CrearPedido()
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $mesa=Mesa::obtenerMesa($this->codigoMesa);
        if($mesa!=null){
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (id,codigo, estado, tiempoDeResolucion,mesa_id) VALUES (:id,:codigo, :estado, :tiempoDeResolucion,:mesa_id)");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':codigo', $this->codigoPedido, PDO::PARAM_STR);
            $consulta->bindValue(':mesa_id', $mesa->id, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $mesa->id, PDO::PARAM_STR);
            $consulta->bindValue(':tiempoDeResolucion', $this->tiempoDeResolucion, PDO::PARAM_STR);
            $consulta->execute();
    
            $pedidoId = $objAccesoDatos->obtenerUltimoId();
            foreach ($this->listaProductos as $producto) {

                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO detalle_pedido (pedido_id, producto_id) VALUES (:pedidoId, :productoId)");
                $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);
                $consulta->bindValue(':productoId', $producto->id, PDO::PARAM_INT);
                $consulta->execute();

            }
        }else{
            echo("se necesita una mesa a la cual asociar el pedido");
        }
        

        return $pedidoId;
    }


    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.codigo, p.estado, p.tiempoDeResolucion, dp.producto_id, pr.precio, pr.cantidad, pr.nombre,pr.sector_id FROM pedido p LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id LEFT JOIN producto pr ON dp.producto_id = pr.id");
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $usuario;
        $pedidos = [];

        $usuario= Usuario::obtenerUsuario($_SESSION['usuario']);
        switch($usuario->rol){
            case "cocinero":
                foreach ($resultados as $fila) {
                    if($fila['sector_id'] == 3){
                        $pedido = new Pedido();
                        $pedido->id = $fila['id'];
                        $pedido->codigoPedido = $fila['codigo'];
                        $pedido->estado = $fila['estado'];
                        $pedido->tiempoDeResolucion = $fila['tiempoDeResolucion'];
            
                        if (!isset($pedidos[$pedido->id])) {
                            $pedidos[$pedido->id] = $pedido;
                        }
            
                        if ($fila['producto_id']) {
                            $producto = new Producto();
                            $producto->id = $fila['producto_id'];
                            $producto->nombre = $fila['nombre'];
                            $producto->precio = $fila['precio'];
                            $producto->cantidad = $fila['cantidad'];
                            $producto->sector = $fila['sector_id'];
                            $pedidos[$pedido->id]->listaProductos[] = $producto;
                        }
                    }
                }
            break;
            case "bartender":
                foreach ($resultados as $fila) {
                    if($fila['sector_id'] == 1){
                        $pedido = new Pedido();
                        $pedido->id = $fila['id'];
                        $pedido->codigoPedido = $fila['codigo'];
                        $pedido->estado = $fila['estado'];
                        $pedido->tiempoDeResolucion = $fila['tiempoDeResolucion'];
            
                        if (!isset($pedidos[$pedido->id])) {
                            $pedidos[$pedido->id] = $pedido;
                        }
            
                        if ($fila['producto_id']) {
                            $producto = new Producto();
                            $producto->id = $fila['producto_id'];
                            $producto->nombre = $fila['nombre'];
                            $producto->precio = $fila['precio'];
                            $producto->cantidad = $fila['cantidad'];
                            $producto->sector = $fila['sector_id'];
                            $pedidos[$pedido->id]->listaProductos[] = $producto;
                        }
                    }
                }
            break;

            case "pastelero":
            break;


        }
        

        return array_values($pedidos);
    }



    public static function obtenerPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.codigo, p.estado, p.tiempoDeResolucion, dp.producto_id, pr.precio, pr.cantidad, pr.nombre FROM pedido p LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id LEFT JOIN producto pr ON dp.producto_id = pr.id WHERE p.codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $pedidos = [];
        foreach ($resultados as $fila) {
            $pedido = new Pedido();
            $pedido->id = $fila['id'];
            $pedido->codigoPedido = $fila['codigo'];
            $pedido->estado = $fila['estado'];
            $pedido->tiempoDeResolucion = $fila['tiempoDeResolucion'];

            if (!isset($pedidos[$pedido->id])) {
                $pedidos[$pedido->id] = $pedido;
            }

            if ($fila['producto_id']) {
                $producto = new Producto();
                $producto->id = $fila['producto_id'];
                $producto->nombre = $fila['nombre'];
                $producto->precio = $fila['precio'];
                $pedidos[$pedido->id]->listaProductos[] = $producto;
            }
        }

        return array_values($pedidos);
    }

    public static function modificarPedido($id, $codigo, $estado, $tiempoDeResolucion)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET codigo = :codigo, estado = :estado, tiempoDeResolucion = :tiempoDeResolucion WHERE id = :id");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoDeResolucion', $tiempoDeResolucion, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET deletionDate = :deletionDate WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':deletionDate', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}

?>
