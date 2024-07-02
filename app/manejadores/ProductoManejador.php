<?php
// TODO Documentar
include_once "./clases/Producto.php";
include_once "./auxiliar/auxiliar.php";
include_once "./interfaces/IManejadores.php";

class ProductoManejador implements IManejadores
{
    public function Alta($request,$response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $nombreProducto = $parametros['nombreProducto'];
        $precioProducto = $parametros['precioProducto'];
        $tipoProducto = $parametros['tipoProducto'];
    
        if(empty($nombreProducto) || empty($precioProducto) || empty($tipoProducto))
        {
            $payload = json_encode(array("mensaje" => "Error al crear producto, uno o mas campos vacios."));
        }
        else
        {
            Producto::AltaProducto($nombreProducto,$precioProducto,$tipoProducto);
            $payload = json_encode(array("mensaje" => "Producto creado con exito"));
        }


    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerUno($request,$response, $args)
    {
        $id = (int)$args['id'];

        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar producto, campo id vacio."));
        }
        else
        {
            $producto = Producto::ConsultarProducto($id);
            
            if($producto)
            {
                $payload = json_encode($producto);
            }
            else
            {
                $payload = json_encode(array("mensaje" => "producto no encontrado."));
            }
        }


    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerTodos($request,$response, $args)
    {
        $listaProductos = Producto::ConsultarTodosLosProductos();

        if(empty($listaProductos))
        {
            $payload = json_encode(array("mensaje" => "No hay Productos"));
        }
        else
        {            
            $payload = json_encode($listaProductos);        
        }


    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function Modificar($request,$response, $args)
    {
        $id = (int)$args['id'];
        
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $nombreProducto = $parametros['nombreProducto'];
        $precioProducto = $parametros['precioProducto'];
        $tipoProducto = $parametros['tipoProducto'];
        
        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar producto, campo id vacio."));
        }
        else
        {
            if(empty($nombreProducto) || empty($precioProducto) || empty($tipoProducto))
            {
                $payload = json_encode(array("mensaje" => "Error al modificar producto, uno o mas campos vacios."));
            }
            else
            {
                Producto::ModificarProducto($nombreProducto,$precioProducto,$tipoProducto,$id);
                $payload = json_encode(array("mensaje" => "producto modificado con exito"));
            }
        }

    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function Baja($request,$response, $args)
    {
        $id = (int)$args['id'];
        
        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar producto, campo id vacio."));
        }
        else
        {
            Producto::BorrarProducto($id);
            $payload = json_encode(array("mensaje" => "El Producto se ha borrado con exito"));

        }

    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }
}

