<?php
require_once 'app/models/Usuario.php';
require_once 'app/interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->rol = $rol;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    
    public function TraerIngresosAlSistema($request, $response, $args)
    {
        $lista = Usuario::ObtenerIngresos();

        $payload = json_encode(array("lista de ingresos al sistema" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function Login($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros['usuario'];
        $contrasena = $parametros['contrasena'];
    
        if (Usuario::verificarCredenciales($usuario, $contrasena)) {        
            $datos = array('usuario' => $usuario);
            $token = AutentificadorJWT::CrearToken($datos);
    
            echo("usuario autentificado");

            //guardo el nombre de usuario 
            $_SESSION['usuario'] = $usuario;

            echo( $_SESSION['usuario']);

            Usuario::registrarIngresoUsuario($usuario);

            $payload = json_encode(array('jwt' => $token));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
          echo("credenciales erroneas");
            $payload = json_encode(array('error' => 'Credenciales inválidas'));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }


    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
   
        $nombre = $parametros['nombre'];


        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
