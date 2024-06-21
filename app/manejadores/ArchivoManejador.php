<?php

include_once "./clases/ArchivoPDF.php";


class ArchivoManejador
{

    public static function Importar($request, $response, $args)
    {
        $archivo = $_FILES['archivo']['tmp_name'];
        if($archivo)
        {
            $archivo = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $titulosColumnas = true;
            foreach ($archivo as $linea) 
            {
                if(!$titulosColumnas)
                {
                    $columnas = explode(',', $linea);
                    $productoNombre = $columnas[1];
                    $productoPrecio = $columnas[2];
                    $productoTipo = $columnas[3];
                    
                    Producto::AltaProducto($productoNombre,$productoPrecio,$productoTipo);
                }
                $titulosColumnas = false;
            }
            $payload = json_encode(array("mensaje" => "Se importaron los productos"));
            $response->getBody()->write($payload);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Error: No se importaron los productos"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function Exportar($request, $response, $args)
    {
        $fecha = new DateTime(date("d-m-Y"));
        $rutaArchivo = "./archivos/csv";
        $titulo = "Productos";

        if (!is_dir($rutaArchivo)) 
        {
            mkdir($rutaArchivo, 0777, true);
        }
    
        $rutaCompleta = $rutaArchivo . "/Lista".$titulo."_".date_format($fecha, 'Y-m-d_H-i-s');

        $archivo = fopen($rutaCompleta.".csv", 'w');

        if(!$archivo)
        {
            $payload = json_encode(array("mensaje"=> "error al abrir el archivo"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        else
        {
            fputcsv($archivo, ['id', 'nombre_producto', 'precio_producto', 'tipo_producto']);
    
            $productos = Producto::ConsultarTodosLosProductos();
    
            foreach ($productos as $producto) 
            {
                fputcsv($archivo, array_slice((array)$producto,4));
            }

            fclose($archivo);

            ArchivoManejador::ExportarPDF($titulo,$productos,$fecha);

            $payload = json_encode(array("mensaje"=> "se exporoto el listado de productos"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }           

    }

    public static function ExportarPDF($titulo,$datos,$fecha)
    {
        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($titulo);

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        
        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('times', 'BI', 12);

        // add a page
        $pdf->AddPage();

        // set some text to print
        $txt = "Lista de ".$titulo.":\n\n";
        foreach ($datos as $dato) 
        {
            foreach ($dato as $key=>$value)
            {
                $txt .= $key. ": " . $value . "\n";
                
            }
            $txt .= "----------------------------------------\n";
        }
    

        // print a block of text using Write()
        $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);

        // ---------------------------------------------------------

        //Close and output PDF document
        $ruta='C:\xampp\htdocs\comanda\app\archivos\pdf';
        $pdf->Output($ruta."/Lista".$titulo."_".date_format($fecha, 'Y-m-d_H-i-s').'.pdf', 'F');

    }

    
        
}
