<?php

// TODO Documentar

include_once "./clases/Mesa.php";
include_once "./auxiliar/auxiliar.php";
include_once "./interfaces/IManejadores.php";

class MesaManejador implements IManejadores
{
    public function Alta($request,$response, $args)
    {
        $claveMesa = Mesa::AltaMesa();
        $payload = json_encode(array("mensaje" => "Mesa creada con exito. Código: ".$claveMesa));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerUno($request,$response, $args)
    {
        $id = $args['id'];

        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar mesa, campo id vacio."));
        }
        else
        {
            $mesa = Mesa::ConsultarMesa($id);
            
            if($mesa)
            {
                $payload = json_encode($mesa);
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Mesa no encontrada."));
            }
        }


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerTodos($request,$response, $args)
    {
        $listaMesas = Mesa::ConsultarTodasLasMesas();

        if(empty($listaMesas))
        {
            $payload = json_encode(array("mensaje" => "No hay mesas"));
        }
        else
        {            
            $payload = json_encode($listaMesas);        
        }


    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function Modificar($request,$response, $args)
    {
        $id = $args['id'];
        
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $estado = $parametros['estadoMesa'];
        
        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar mesa, campo id vacio."));
        }
        else
        {
            if(empty($estado))
            {
                $payload = json_encode(array("mensaje" => "Error al modificar mesa, uno o mas campos vacios."));
            }
            else
            {
                Mesa::ModificarMesa($estado,$id);
                $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));
            }
        }

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
        
    }

    public function Baja($request,$response, $args)
    {
        $id = $args['id'];
        
        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar mesa, campo id vacio."));
        }
        else
        {
            Mesa::BorrarMesa($id);
            $payload = json_encode(array("mensaje" => "La mesa se ha borrado con exito"));

        }

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    }

    
}

