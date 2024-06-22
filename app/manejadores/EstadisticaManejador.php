<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";
date_default_timezone_set('America/Argentina/Buenos_Aires');

class EstadisticaManejador
{
       
    public function ObtenerMejoresEncuestas($request,$response, $args)
    {
    
        $usuario = Encuesta::ConsultarTopCincoEncuestas();
        
        if($usuario)
        {
            $payload = json_encode($usuario);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Encuestas no encontradas."));
        }
    


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerMesaMasUsada($request,$response, $args)
    {
    
        $pedidoMesa = Pedido::ConsultarPedidoMesaMasUsada();

        $mesa = Mesa::ConsultarMesa($pedidoMesa->id_mesa);
        
        if($mesa)
        {
            $payload = json_encode($mesa);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Mesa no encontrada."));
        }
    


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerPedidoEntregadosFueraDeTiempo($request,$response, $args)
    {
    
        $pedidos = TiempoEspera::ConsultarTiempoEsperaTardios();

        
        if($pedidos)    
        {
            $payload = json_encode($pedidos);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay pedidos tardios."));
        }
    


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    


    }

    public function ObtenerOperacionesUsuarios($request,$response, $args)
    {
    
        $pedidos = TiempoEspera::ConsultarTiempoEsperaTardios();

        
        if($pedidos)    
        {
            $payload = json_encode($pedidos);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay pedidos tardios."));
        }
    


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    

        
    }
    
    
} 





