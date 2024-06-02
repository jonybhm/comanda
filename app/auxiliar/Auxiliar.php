<?php
function CargarArchivo($archivoNombre)
/**
 * Devuelve un array con los datos del json
 * 
 * Esta funcion se utiliza para obtener la 
 * informacion dentro de un archivo .json
 * y recibirlo en un array asociativo
 * 
 * @param string $archivoNombre ruta al archivo .json  
 */
{

    if(file_exists($archivoNombre))
    {
        $array = json_decode(file_get_contents($archivoNombre),true);
    }
    else
    {
        $array = array();
    }

    return $array;
} 

function AutoincrementarId($archivoNombre)
{    
    $array= CargarArchivo($archivoNombre);
    $ultimoId = count($array);

    if($ultimoId == 0)
    {
        $nuevoId = 1;
    }
    else
    {
        $nuevoId = $ultimoId + 1;
    }

    return $nuevoId;
}

function VerificarFecha($strFecha)
{
    if($strFecha != NULL)
    {
        $arrayFecha = explode(" ",$strFecha);
        $fechaValida = checkdate($arrayFecha[1],$arrayFecha[0],$arrayFecha[2]);
    }
    else
    {
        $fechaValida = true;
    }

    
        
    return $fechaValida;

}

function MostrarDatosArrayVenta($arrayVentas)
{
    foreach($arrayVentas as $venta)
    {
        echo PHP_EOL."-------------Venta-------------".PHP_EOL;
        echo 
        PHP_EOL."mail: ".$venta["mail"].
        PHP_EOL."fecha: ".$venta["fecha"].
        PHP_EOL."numero pedido: ".$venta["numeroPedido"].
        PHP_EOL."id venta: ".$venta["id"].
        PHP_EOL."marca: ".$venta["info"]["marca"].
        PHP_EOL."tipo: ".$venta["info"]["tipo"].
        PHP_EOL."modelo: ".$venta["info"]["modelo"].
        PHP_EOL."cantidad: ".$venta["info"]["cantidad"].
        PHP_EOL."precio: ".$venta["info"]["precioFinal"].
        PHP_EOL;
    }
}

function MostrarDatosArrayProducto($arrayProductos)
{
    foreach($arrayProductos as $producto)
    {
        echo PHP_EOL."-------------Producto-------------".PHP_EOL;
        echo 
        PHP_EOL."marca: ".$producto["marca"].
        PHP_EOL."modelo: ".$producto["modelo"].
        PHP_EOL."tipo: ".$producto["tipo"].
        PHP_EOL."color: ".$producto["color"].
        PHP_EOL."precio: ".$producto["precio"].
        PHP_EOL."stock: ".$producto["stock"].
        PHP_EOL."id: ".$producto["id"].
        PHP_EOL;
    }
}

function MostrarDatosArrayDevoluciones($devolucion)
{
    echo PHP_EOL."-------------Devolucion-------------".PHP_EOL;
    echo 
    PHP_EOL."id devolucion: ".$devolucion["id"].
    PHP_EOL."motivo: ".$devolucion["motivo"].
    PHP_EOL."numero pedido: ".$devolucion["pedido"].
    PHP_EOL;  
   
}

function MostrarDatosArrayCupones($cupon)
{
    echo PHP_EOL."-------------Cupon-------------".PHP_EOL;
    echo 
    PHP_EOL."id cupon: ".$cupon["id"].
    PHP_EOL."id devolucion: ".$cupon["devolucion"].
    PHP_EOL."descuento: ".$cupon["descuento"]."%".
    PHP_EOL."estado: ".$cupon["estado"].
    PHP_EOL;

}