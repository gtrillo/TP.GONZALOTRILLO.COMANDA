<?php

class Mesa {

    public $id;
    public $estado;
    public $foto;
    public $codigoMesa;
    public $fecha;

    public function CrearMesa()
    {
        $fecha = new DateTime(date("d-m-Y"));
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (id, codigoMesa, estado, foto, fecha) VALUES (:id, :codigoMesa, :estado, :foto, :fecha)");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->execute();
    
        return $objAccesoDatos->obtenerUltimoId();
    }
    

    public static function obtenerTodos()
    {
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, foto,codigoMesa,fecha FROM mesa");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }


    public static function obtenerMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado,codigoMesa, foto,fecha FROM mesa WHERE codigoMesa = :codigo");
        $consulta->bindValue(':codigo', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function obtenerMesaxId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado,codigoMesa, foto,fecha FROM mesa WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET codigoMesa = :codigocodigoMesa, estado = :estado, foto = :foto, fecha=:fechaWHERE id = :id");
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function FacturacionMesas()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("
            SELECT m.id AS mesa_id, COALESCE(SUM(p.total), 0) AS total_facturado
            FROM mesa m
            LEFT JOIN pedido p ON m.id = p.mesa_id
            GROUP BY m.id
            ORDER BY total_facturado DESC
        ");
        $consulta->execute();
        $mesas = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $mesas;
    }
    
    
    public static function modificarEstadoMesa($codigo, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $usuario = Usuario::obtenerUsuario($_SESSION['usuario']);
    
        // Verificar si el usuario tiene el rol de administrador
        if ($usuario->getRol($_SESSION['usuario']) === 'administrador' && $estado === 'cerrada') {
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estado = :estado WHERE codigoMesa = :codigo");
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $resultado = $consulta->execute();
    
            return $resultado;
        } elseif ($estado !== 'cerrada') {
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estado = :estado WHERE codigoMesa = :codigo");
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
        if($nuevoValor == "con cliente comiendo" ||  $nuevoValor ==" con cliente esperando pedido" || $nuevoValor == "con cliente pagando" || $nuevoValor == "cerrada") {
            return true;
        }

        return $retorno;

    }

    public static function ObtenerFacturacionEntreFechas($codigoMesa, $fechaInicio, $fechaFinalizacion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(p.total) AS total_facturacion FROM pedido p 
        JOIN mesa m ON p.mesa_id = m.id 
        WHERE m.codigoMesa = :codigoMesa 
        AND m.fecha BETWEEN :fechaInicio AND :fechaFinalizacion");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        $consulta->bindValue(':fechaFinalizacion', $fechaFinalizacion, PDO::PARAM_STR);
        $consulta->execute();
    
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        return $resultado['total_facturacion'];
    }
    

    public static function obtenerMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, foto,codigoMesa,fecha, COUNT(*) AS usos FROM mesa GROUP BY codigoMesa ORDER BY usos DESC LIMIT 1");
        $consulta->execute();
    
        return $consulta->fetchObject('Mesa');
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