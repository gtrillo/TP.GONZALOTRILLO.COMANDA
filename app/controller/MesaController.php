<?php
require_once 'app/models/mesa.php';
require_once 'app/interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
  {
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $estado = $parametros['estado'];
        $foto = $parametros['foto'];
        $codigoMesa = $parametros['codigoMesa'];
        $fecha= $parametros['fecha'];
        
        $mesa = new Mesa();
        $mesa->estado = $estado;
        $mesa->foto = $foto;
        $mesa->fecha = $fecha;
        $mesa->codigoMesa = $codigoMesa;
        $mesa->CrearMesa();
    
        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function RankingFacturacionMesas($request, $response, $args)
    {
        $lista = Mesa::FacturacionMesas();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
  
    public function TraerUno($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $mesa = Mesa::obtenerMesa($codigo);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerMasUsada($request, $response, $args)
    {
        $mesa = Mesa::obtenerMasUsada();
        $payload = json_encode($mesa);
    
        $payloadWithText = "Mesa mas usada\n" . $payload;
    
        $response->getBody()->write($payloadWithText);
        return $response->withHeader('Content-Type', 'application/json');
    }
    


    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function FacturacionDeUnaMesa($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        $codigoMesa = $parametros['codigoMesa'];
        $fechaInicio = $parametros['fechaInicio'];
        $fechaFinalizacion = $parametros['fechaFinalizacion'];
    
        $facturacion = Mesa::ObtenerFacturacionEntreFechas($codigoMesa, $fechaInicio, $fechaFinalizacion);
    
        $payload = json_encode(array("facturacion" => $facturacion));
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    


    public function ModificarEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $codigo = $parametros['codigo'];
        $numero_pedido = $parametros['numero_pedido'];
        $estado = $parametros['estado'];
        $tiempo_finalizacion = $parametros['tiempo_finalizacion'];

        if (Mesa::modificarEstadoMesa($codigo, $estado)) {
          Pedido::actualizarHorarioDeFinalizacion($numero_pedido, $tiempo_finalizacion);
            $payload = json_encode(array("mensaje" => "Estado de mesa modificado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al modificar la mesa"));
        }
          
        $response->getBody()->write($payload);
    
        $fotoMesa = $request->getUploadedFiles()['foto_mesa'];
    
        if ($fotoMesa && $fotoMesa->getError() === UPLOAD_ERR_OK) {
            $nombreArchivo = uniqid('mesa_') . '.' . $fotoMesa->getClientFilename();
    
            $fotoMesa->moveTo('app/imagenes/mesa/' . $nombreArchivo);
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $cantidad = $parametros['cantidad'];
        $id = $parametros['id'];

        Mesa::modificarMesa($nombre, $precio, $cantidad,$id);

        $payload = json_encode(array("mensaje" => "Producto modificado con éxito"));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesaId = $parametros['mesaId'];
        Mesa::borarMesa($mesaId);
        $payload = json_encode(array("mensaje" => "Mesa borrado con exito"));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

   
}
