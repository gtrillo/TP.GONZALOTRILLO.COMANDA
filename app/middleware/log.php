<?php
require_once 'app/bd/AccesoDatos.php';
class RegistroMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    public function __invoke($request, $handler)
    {
    
        $response = $handler->handle($request);

        $productoId = $request->getAttribute('routeInfo')[2]['id'];
        $usuarioId = $_SESSION['id_usuario'];

        $fechaAccion = date('Y-m-d H:i:s');
        $accion = 'Borrado';

        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("INSERT INTO logs (id_usuario, id_arma, accion, fecha_accion) VALUES (:usuarioId, :productoId, :accion, :fechaAccion)");
        $consulta->bindValue(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $consulta->bindValue(':productoId', $productoId, PDO::PARAM_INT);
        $consulta->bindValue(':accion', $accion);
        $consulta->bindValue(':fechaAccion', $fechaAccion);
        $consulta->execute();

        return $response;
    }
}

?>