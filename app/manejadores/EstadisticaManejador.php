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
    
      
        $tipoEmpleado = (string)$args['tipoEmpleado'];

        if(empty($tipoEmpleado))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar usuario, campo tipoEmpleado vacio."));
        }
        else
        {
            
            $usuario = Usuario::ConsultarCantidadOPeracionesUsuarioSector($tipoEmpleado);
            
            if($usuario)
            {
                $payload = json_encode($usuario);
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Usuario no encontrado."));
            }
        }


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');        

        
    }

    public function ObtenerOperacionesUsuarioEspecifico($request,$response, $args)
    {     
        $tipoEmpleado = (string)$args['tipoEmpleado'];
        $nombreUsuario = (string)$args['nombreUsuario'];

        if(empty($tipoEmpleado))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar usuario, campo tipoEmpleado vacio."));
        }
        else
        {
            
            $usuario = Usuario::ConsultarCantidadOPeracionesUsuarioSectoryNombre($tipoEmpleado,$nombreUsuario);
            
            if($usuario)
            {
                $payload = json_encode($usuario);
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Usuario no encontrado."));
            }
        }


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');        

        
    }
    
    public function ObtenerLogeoUsuarioEspecifico($request,$response, $args)
    {
        $nombreUsuario = (string)$args['nombreUsuario'];

        if(empty($nombreUsuario))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar usuario, campo nombre Usuario vacio."));
        }
        else
        {
            
            $usuario = Usuario::ConsultarLogeoUsuario($nombreUsuario);
            
            if($usuario)
            {
                $payload = json_encode($usuario);
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Usuario no encontrado."));
            }
        }


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');        

        
    }

    public function ObtenerPedidoVendidosDescendente($request,$response, $args)
    {
    
        $pedidos = Producto::ConsultarProductosDelMasVendidoAlMenos();

        
        if($pedidos)    
        {
            $payload = json_encode($pedidos);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay pedidos."));
        }
    


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    


    }

    public function ObtenerMesasPorOrdenDeFacturacion($request,$response, $args)
    {
    
        $mesas = Mesa::ConsultarMesasPorOrdenDeFacturacion();

        
        if($mesas)    
        {
            $payload = json_encode($mesas);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay mesas."));
        }
    


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    


    }

    public function ObtenerMesasEntreFechas($request,$response, $args)
    {
    
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $idMesa = $parametros['idMesa'];
        $fechaMin = $parametros['fechaMin'];
        $fechaMax = $parametros['fechaMax'];
        
        $mesas = Mesa::ConsultarFacturacionMesasEntreFechas($fechaMin,$fechaMax,$idMesa);

        
        if($mesas)    
        {
            $payload = json_encode($mesas);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay mesas."));
        }
    


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    


    }
    
} 





