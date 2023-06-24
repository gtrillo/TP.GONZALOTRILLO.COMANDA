<?php
require_once 'app/models/pedido.php';
class Mesa {

    public $id;
    public $estado;
    public $foto;
    public $codigoMesa;
    public $pedido;
    public $codigoPedido;
    public function CrearMesa()
    {
        $pedido = Pedido::obtenerPedido($this->codigoPedido);
        if($pedido != null){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (id,codigo ,estado, foto,pedido_id) VALUES (:id,:codigo,:estado,:foto,:pedido_id)");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $this->codigoMesa, PDO::PARAM_STR);
            echo($pedido->id);
            $consulta->bindValue(':pedido_id', $pedido->id, PDO::PARAM_STR);
            $consulta->execute();
        }else{
            echo("el codigo de pedido es incorrecto intente nuevamente");
        }


        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, foto,codigo, cantidad FROM mesa");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }



    
    public static function obetenerMesa($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado,codigo, foto FROM mesa WHERE usuario = :usuario");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET codigo = :codigo, estado = :estado, foto = :foto WHERE id = :id");
        $consulta->bindValue(':codigo', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarMesa($codigo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET deletionDate = :deletionDate WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $codigo, PDO::PARAM_INT);
        $consulta->bindValue(':deletionDate', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }


}

  


?>