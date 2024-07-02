<?php

// TODO Documentar

include_once "./clases/Pedido.php";
include_once "./auxiliar/auxiliar.php";
include_once "./interfaces/IManejadores.php";

class PedidoManejador implements IManejadores
{
    public function Alta($request,$response, $args)
    {
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $idMesa = $parametros['idMesa'];
        $nombreCliente = $parametros['nombreCliente'];

        Pedido::AltaPedido($idMesa,$nombreCliente);
        $payload = json_encode(array("mensaje" => "Pedido creada con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerUno($request,$response, $args)
    {
        $id = $args['id'];

               
        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar pedido, campo id vacio."));
        }
        else
        {
            $pedido = Pedido::ConsultarPedido($id);
            
            if($pedido)
            {
                $payload = json_encode($pedido);
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Pedido no encontrado."));
            }
        }


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerTodos($request,$response, $args)
    {
        $listaPedidos = Pedido::ConsultarTodosLosPEdidos();

        if(empty($listaPedidos))
        {
            $payload = json_encode(array("mensaje" => "No hay pedidos"));
        }
        else
        {            
            $payload = "";
            
            foreach ($listaPedidos as $pedido)
            {
                $pedido = Encuesta::ConsultarTiempoPedido($pedido->id_mesa,$pedido->id);
                
                if($pedido && $pedido->estado != "entregado")
                {
    
                    $diferencia = CalcularDiferenciaTiempoEnMinutos($pedido->registrado,(int)$pedido->estimado);
                    
                    if($diferencia < 0 )
                    {
                        $mensaje = "pedido retrasado. Tiempo extra: ". $diferencia;
                    }
                    else
                    {
                        $mensaje = "tiempo restante: ".$diferencia." minutos";
                    }
                    
                    $payload .= json_encode(array("pedido"=>$pedido,"mensaje"=>$mensaje));
                }
                else if( $pedido->estado == "entregado")
                {
                    $payload .= json_encode(array("pedido"=>$pedido,"mensaje" => "Pedido entregado."));
                }
                else
                {
                    $payload .= json_encode(array("mensaje" => "Pedido no encontrado."));
    
                }

            }
            
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

        $estadoPedido = $parametros['estadoPedido'];
        $tiempoPreparacion = $parametros['tiempoPreparacion'];
        $precioTotal = $parametros['precioTotal'];

        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar pedido, campo id vacio."));
        }
        else
        {
            if(empty($estadoPedido))
            {
                $payload = json_encode(array("mensaje" => "Error al modificar pedido, uno o mas campos vacios."));
            }
            else
            {
                Pedido::ModificarPedido($estadoPedido,$tiempoPreparacion,$precioTotal,$id);
                $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
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
            $payload = json_encode(array("mensaje" => "Error al buscar pedido, campo id vacio."));
        }
        else
        {
            Pedido::BorrarPedido($id);
            $payload = json_encode(array("mensaje" => "La pedido se ha borrado con exito"));

        }

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    }

    
}

