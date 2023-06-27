<?php
require_once 'app/models/Producto.php';
require_once 'app/interfaces/IApiUsable.php';
use League\Csv\Reader;
use League\Csv\Writer;

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $precio = $parametros['precio'];
        $nombre = $parametros['nombre'];
        $cantidad = $parametros['cantidad'];
        $sector = $parametros['sector'];

        // Creamos el producto
        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->cantidad = $cantidad;
        $producto->precio = $precio;
        $producto->sector = $sector;

        $producto->CrearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con éxito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function CargarCsv($request, $response, $args) {
        $uploadedFiles = $request->getUploadedFiles();
        $file = $uploadedFiles['archivo'];
        if ($file->getError() === UPLOAD_ERR_OK) {
            $tempFilePath = 'app/importacionesCSV/' . $file->getClientFilename();
            $file->moveTo($tempFilePath);
    
            $csv = \League\Csv\Reader::createFromPath($tempFilePath, 'r');
            $csv->setDelimiter(',');
            $csv->setHeaderOffset(0);
    
            foreach ($csv->getRecords() as $record) {
                $nombre = $record['nombre'];
                $cantidad = $record['cantidad'];
                $precio = $record['precio'];
                $sector = $record['sector'];
    
                $producto = new Producto();
                $producto->nombre = $nombre;
                $producto->cantidad = $cantidad;
                $producto->precio = $precio;
                $producto->sector = $sector;
    
                $producto->CrearProducto();
            }

            unlink($tempFilePath);

            $payload = json_encode(array("mensaje" => "archivo cargado con éxito"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("mensaje" => "error al cargar el archivo"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
    
    
    public function DescargarCsv($request, $response, $args) {
        $lista = Producto::obtenerTodos();

        $csv = Writer::createFromPath('exportacionescsv', 'w+');
        $csv->setDelimiter(',');
        $csv->setNewline("\r\n");
    
        $csv->insertOne(['id', 'nombre', 'cantidad', 'precio', 'id_sector', 'deletioDate']);

        foreach ($lista as $producto) {
            $csv->insertOne([
                $producto->id,
                $producto->nombre,
                $producto->cantidad,
                $producto->precio,
                $producto->sector,
            ]);
        }
    
        $csvContent = $csv->getContent();
    
        $response = $response->withHeader('Content-Type', 'text/csv')
                             ->withHeader('Content-Disposition', 'attachment; filename="archivo.csv"');

        $response->getBody()->write($csvContent);
    
        return $response;
    }
    

    public function TraerUno($request, $response, $args)
    {
        $productoId = $args['id'];
        $producto = Producto::obtenerProductoXId($productoId);
        $payload = json_encode($producto);
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function CrearUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $nombre = $parametros['nombre'];
        $cantidad = $parametros['cantidad'];
        $precio = $parametros['precio'];
    
        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->cantidad = $cantidad;
        $producto->precio = $precio;
        $producto->crearProducto();
    
        $payload = json_encode(array("mensaje" => "Producto creado con éxito"));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    /*
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $productoId = $args['id'];
        $nombre = $parametros['nombre'];
        $cantidad = $parametros['cantidad'];
        $precio = $parametros['precio'];
    
        $producto = new Producto();
        $producto->id = $productoId;
        $producto->nombre = $nombre;
        $producto->cantidad = $cantidad;
        $producto->precio = $precio;
        $producto->actualizarProducto();
    
        $payload = json_encode(array("mensaje" => "Producto actualizado con éxito"));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }*/


    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $cantidad = $parametros['cantidad'];
        $id = $parametros['id'];

        Producto::modificarProducto($nombre, $precio, $cantidad,$id);

        $payload = json_encode(array("mensaje" => "Producto modificado con éxito"));

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
