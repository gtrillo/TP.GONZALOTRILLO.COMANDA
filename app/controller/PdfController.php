<?php

use Psr\Http\Message\ResponseInterface as Response;

class PdfController {

    public function descargarPdf($request, $response, $args): Response {
        $rutaLogo = 'app/logoEmpresa/logo.pdf';
    
        // Verificar si el archivo existe
        if (file_exists($rutaLogo)) {
            // Crear una nueva instancia de Psr\Http\Message\ResponseInterface
            $slimResponse = $response->withHeader('Content-Type', 'application/pdf')
                                     ->withHeader('Content-Disposition', 'attachment; filename="logo.pdf"');
            
            // Leer el contenido del archivo y escribirlo en la respuesta
            $fileContent = file_get_contents($rutaLogo);
            $slimResponse->getBody()->write($fileContent);
    
            return $slimResponse;
        } else {
            // El archivo no existe
            $response->getBody()->write('El archivo no pudo ser encontrado.');
            return $response->withStatus(404)->withHeader('Content-Type', 'text/plain');
        }
    }
}


?>