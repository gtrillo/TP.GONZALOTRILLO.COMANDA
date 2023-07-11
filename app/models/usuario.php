<?php
require_once 'app/bd/AccesoDatos.php';
class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $rol;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave,rol) VALUES (:usuario, :clave,:rol)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public function getRol($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT rol FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return $resultado['rol'];
        } else {
            return null; // o un valor por defecto para indicar que no se encontró el usuario
        }
    }

    public static function verificarCredenciales($usuario, $contrasena)
    {
        $retorno = false;

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT clave FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();
    
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        
        //$_SESSION['id_usuario'] = $resultado['id'];

        if (password_verify($contrasena, $resultado['clave'])) {
              $retorno = true;
        } 
    
        return $retorno;
    }

    public function registrarIngresoUsuario($nombreUsuario) {
        $fechaIngreso = date('Y-m-d');
        $horaIngreso = date('H:i:s');
    
        $usuarioId = null;
    
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id from Usuarios where usuario=:nombreUsuario");
        $consulta->bindValue(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
        $consulta->execute();
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
        $usuarioId = $usuario['id'];
    
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO registro_ingreso (usuario_id, fecha_ingreso, hora_ingreso) VALUES (:usuarioId, :fechaIngreso, :horaIngreso)");
        $consulta->bindValue(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $consulta->bindValue(':fechaIngreso', $fechaIngreso, PDO::PARAM_STR);
        $consulta->bindValue(':horaIngreso', $horaIngreso, PDO::PARAM_STR);
    
        $consulta->execute();
    }
    
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }


    public static function ObtenerIngresos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT u.usuario, r.fecha_ingreso,r.hora_ingreso from registro_ingreso r join usuarios u on u.id = r.usuario_id");
        $consulta->execute();
        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
            
        return $usuarios;
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario,rol, clave FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET deletionDate = :deletionDate WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':deletionDate', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}