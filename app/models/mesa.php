<?php

class Mesa {

    public $id;
    public $estado;
    public $foto;
    public $codigoMesa;

    public function CrearMesa()
    {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (id,codigo ,estado, foto) VALUES (:id,:codigo,:estado,:foto)");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $this->codigoMesa, PDO::PARAM_STR);
            $consulta->execute();
        

        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function obtenerTodos()
    {
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, foto,codigo, cantidad FROM mesa");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }


    public static function obtenerMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado,codigo, foto FROM mesa WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function obtenerMesaxId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado,codigo, foto FROM mesa WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
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

    public static function modificarEstadoMesa($codigo, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        if (self::validarEstado($estado)) {
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estado = :estado WHERE codigo = :codigo");
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $resultado = $consulta->execute();
    
            return $resultado; 
        }
    
        return false; 
    }


    private static function validarEstado($nuevoValor)
    {
        $retorno = false;
        $nuevoValor = strtolower($nuevoValor);
        if($nuevoValor == "con cliente esperando pedido" || $nuevoValor == "con cliente pagando" || $nuevoValor == "cerrada") {

            return true;
        }

        return $retorno;

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