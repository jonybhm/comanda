<?php
// TODO Documentar 

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";
date_default_timezone_set('America/Argentina/Buenos_Aires');

/**
 * La clase EstadisticaManejador() 
 */
class EstadisticaManejador
{
    #=============================MESAS MAS USADAS=============================

    public function ObtenerMesaMasUsada($request,$response, $args)
    {
    
        $pedidoMesa = Pedido::ConsultarPedidoMesaMasUsada();

        $mesa = Mesa::ConsultarMesa($pedidoMesa[0]->id_mesa);
        
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

    #=============================MESAS MENOS USADAS=============================

    public function ObtenerMesaMenosUsada($request,$response, $args)
    {
    
        $pedidoMesa = Pedido::ConsultarPedidoMesaMenosUsada();

        $mesa = Mesa::ConsultarMesa($pedidoMesa[0]->id_mesa);
        
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

    #=============================MESAS MAYOR FACTURACION=============================

    public function ObtenerMesasMayorFacturacion($request,$response, $args)
    {
    
        $mesas = Mesa::ConsultarMesasPorOrdenDeFacturacionDescendente();

        
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


    #=============================MESAS MENOR FACTURACION=============================

    public function ObtenerMesasMenorFacturacion($request,$response, $args)
    {
    
        $mesas = Mesa::ConsultarMesasPorOrdenDeFacturacionAscendente();

        
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

    #=============================MESAS MAYOR IMPORTE=============================


    public function ObtenerMesaMayorImporte($request,$response, $args)
    {
    
        $mesas = Mesa::ConsultarMesaMayorImporte();

        
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

    #=============================MESAS MENOR IMPORTE=============================

    public function ObtenerMesaMenorImporte($request,$response, $args)
    {
    
        $mesas = Mesa::ConsultarMesaMenorImporte();

        
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


    #=============================MESAS ENTRE FECHAS=============================

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

    #=============================MESAS MEJOR PUNTAJE=============================

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

    #=============================MESAS PEOR PUNTAJE=============================

    public function ObtenerPeoresEncuestas($request,$response, $args)
    {
    
        $usuario = Encuesta::ConsultarBottomCincoEncuestas();
        
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

    #=============================PRODUCTO MAS VENDIDO=============================

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
    
    #=============================PRODUCTO MENOS VENDIDO=============================

    public function ObtenerPedidoVendidosAscendente($request,$response, $args)
    {
    
        $pedidos = Producto::ConsultarProductosDelMenosVendidoAlMas();

        
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
    
    #=============================PEDIDOS FUERA DE TIEMPO=============================

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

    #=============================PEDIDOS CANCELADOS=============================

    public function ObtenerPedidosCancelados($request,$response, $args)
    {
    
        $pedidos = Pedido::ConsultarCancelados();
        
        if($pedidos)    
        {
            $payload = json_encode($pedidos);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay pedidos cancelados."));
        }


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    

    }

    #=============================LOGEO DE USUARIO=============================

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

    #=============================OPERACIONES USUARIO POR SECTOR=============================

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

    #=============================OPERACIONES USUARIO POR NOMBRE=============================

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
            
            $usuario = Usuario::ConsultarCantidadOPeracionesUsuarioNombre($tipoEmpleado,$nombreUsuario);
            
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
 
    #=============================OPERACIONES POR SECTOR Y NOMBRE=============================

    public function ObtenerOperacionesUsuarioPorSector($request,$response, $args)
    {     
        $tipoEmpleado = (string)$args['tipoEmpleado'];

        if(empty($tipoEmpleado))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar usuario, campo tipoEmpleado vacio."));
        }
        else
        {
            
            $usuario = Usuario::ConsultarCantidadOPeracionesUsuarioSectoryNombre($tipoEmpleado);
            
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

    
    
}