<?php
class Encuesta {

    public $id;
    public $pedidoId;
    public $nombreCliente;
    public $puntuacionMesa;
    public $puntuacionRestaurante;
    public $puntuacionMozo;
    public $puntuacionCocinero;
    public $textoExperiencia;
    public $fechaCreacion;
    public $numeroPedido;
    
    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        // Consulta para obtener el ID del pedido
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM pedido WHERE codigo = :numeroPedido");
        $consulta->bindValue(':numeroPedido', $this->numeroPedido, PDO::PARAM_INT);
        $consulta->execute();
        
        // Obtener el resultado de la consulta
        $pedidoId = $consulta->fetchColumn();
        
        // Insertar la encuesta utilizando el ID del pedido obtenido
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta (pedidoId, nombreCliente, puntuacionMesa, puntuacionRestaurante, puntuacionMozo, puntuacionCocinero, textoExperiencia) VALUES (:pedidoId, :nombreCliente, :puntuacionMesa, :puntuacionRestaurante, :puntuacionMozo, :puntuacionCocinero, :textoExperiencia)");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionRestaurante', $this->puntuacionRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionMozo', $this->puntuacionMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionCocinero', $this->puntuacionCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':textoExperiencia', $this->textoExperiencia, PDO::PARAM_STR);
        $consulta->execute();
    
        return $objAccesoDatos->obtenerUltimoId();
    }
    

    public static function obtenerEncuesta($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchObject('Encuesta');
    }
    public static function obtenerMejoresEncuestas($cantidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta ORDER BY puntuacionMesa DESC, puntuacionRestaurante DESC, puntuacionMozo DESC, puntuacionCocinero DESC LIMIT :cantidad");
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $resultados;
    }
}
?>
