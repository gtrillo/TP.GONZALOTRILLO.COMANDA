<?php

class Producto {
    public $id;
    public $nombre;
    public $precio;
    public $cantidad;
    public $sector;


    public function CrearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        $consultaExiste = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM sector WHERE nombre = :nombre");
        $consultaExiste->bindValue(':nombre', $this->sector, PDO::PARAM_STR);
        $consultaExiste->execute();
        
        $sectorExiste = $consultaExiste->fetchColumn();
        
        if ($sectorExiste) {
            $consultaIdSector = $objAccesoDatos->prepararConsulta("SELECT id FROM sector WHERE nombre = :nombre");
            $consultaIdSector->bindValue(':nombre', $this->sector, PDO::PARAM_STR);
            $consultaIdSector->execute();
            
            $idSector = $consultaIdSector->fetchColumn();
            
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO producto (id, nombre, precio, cantidad, sector_id) VALUES (:id, :nombre, :precio, :cantidad, :sector_id)");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':sector_id', $idSector, PDO::PARAM_INT);
        
            $consulta->execute();
        
            return $objAccesoDatos->obtenerUltimoId();
        } else {
            throw new Exception("El sector ingresado no es válido.");
        }
    }
    


    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, cantidad,sector_id FROM producto");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProductoXId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, cantidad,sector_id FROM producto WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function obtenerProductoXNombre($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, cantidad,sector_id FROM producto WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }
    

    public function modificarProducto($nombre, $precio, $cantidad, $id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE producto SET nombre = :nombre, precio = :precio, cantidad = :cantidad WHERE id = :id");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $precio, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
    
        public function obtenerProductosMasVendidos() {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
            $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.nombre, COUNT(*) as cantidad_vendida
                FROM detalle_pedido AS dp
                JOIN producto AS p ON dp.producto_id = p.id
                GROUP BY dp.producto_id
                ORDER BY cantidad_vendida DESC
            ");
    
            $consulta->execute();
            $productos = $consulta->fetchAll(PDO::FETCH_ASSOC);
            
            return $productos;
        }

    public function borrarProducto($request, $response, $args)
    {
        $id = $args['id'];

        $producto = new Producto();
        $producto->id = $id;
        $producto->borrarProducto();

        $payload = json_encode(array("mensaje" => "Producto borrado con éxito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }




    
}



?>