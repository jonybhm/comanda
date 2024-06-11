<?php
include_once "./clases/Pedido.php";
include_once "./clases/Mesa.php";

include_once "./clases/Servicio.php";
include_once "./auxiliar/auxiliar.php";

class ServicioManejador
{
    #---------------------------TOMA DE PEDIDOS---------------------------

    public function TomarPedido($request, $response, $args)
    {

        $jsonData = file_get_contents('php://input');
        $parametros = json_decode($jsonData,true);
        
        $idMesa = $parametros["idMesa"];
        $nombreCliente = $parametros["nombreCliente"];

        $mesa = Mesa::ConsultarMesa($idMesa);

        foreach($mesa as $key=>$value)
        {
            if($key=="estado_mesa")
            {
                $mesaEstado = $value;
            }
        }

        if($mesaEstado!="cerrada")
        {
            $payload = json_encode(array("mensaje" => "La mesa esta ocupada"));

        }
        else
        {

            $idPedido = Pedido::AltaPedido($idMesa,$nombreCliente);
    
            Mesa::ModificarMesa("con cliente esperando el pedido",$idMesa);
    
            if(!$idPedido)
            {
                $payload = json_encode(array("mensaje" => "Error al crear el pedido"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json'); 
            }
    
            $productosDelPedido = array_slice($parametros,2);
            
            $precioTotal = 0;
            $contenidoMensaje = "";
                          
            foreach($productosDelPedido as $producto)
            {
                if(empty($producto["nombre"]) || empty($producto["cantidad"]))
                {
                    $payload = json_encode(array("mensaje" => "Error al crear producto, uno o mas campos vacios."));
                }
                else
                {
    
                    $precioParcial = Servicio::AltaProductosPedido($idPedido,$idMesa,$nombreCliente,$producto["nombre"], $producto["cantidad"]);
                    $contenidoMensaje = $contenidoMensaje.PHP_EOL.$producto["nombre"]." agregado al pedido X ".$producto["cantidad"]." por un precio $".$precioParcial; 
                }
                    
                $precioTotal = $precioTotal + $precioParcial;
                    
                    
            }
            $payload = json_encode(array("mensaje" => $contenidoMensaje.PHP_EOL."Por un total de $".$precioTotal));

            Pedido::ModificarPrecioPedido($precioTotal,$idMesa);
        }

    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    #---------------------------LLEGADA PEDIDOS A COCINA/BARRA---------------------------

    public function RecibirPedidosPendientes($request,$response, $args)
    {

        $params = $request->getQueryParams();

        if(isset($params["credenciales"]))
        {
            $tipoPedido = "";
            $credenciales = $params ["credenciales"];
            
            switch($credenciales)
            {
                case "cocinero":
                    $tipoPedido = "comida";
                    break;
                case "bartender":
                    $tipoPedido = "bebida";
                    break;                                        
            }
        }

        $estadoPedido = "pendiente";

               
        if(empty($estadoPedido))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar pedido, no hay pendientes"));
        }
        else
        {
            $pedido = Pedido::ConsultarPedidoPorEstado($estadoPedido,$tipoPedido);
            
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

    

    #---------------------------CAMBIAR ESTADO DE LOS PEDIDOS---------------------------


    public function ModificarPedidosPendientes($request,$response, $args)
    {
        $idMesa = $args['idMesa'];
        
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $estadoPedidoProducto = $parametros['estadoProductoPedido'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $nombreProducto = $parametros['nombreProducto'];
        
        if(empty($idMesa))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar la mesa del pedido, campo id vacio."));
        }
        else
        {
            if(empty($estadoPedidoProducto) || empty($tiempoEstimado))
            {
                $payload = json_encode(array("mensaje" => "Error al modificar pedido de mesa ".$idMesa.", uno o mas campos vacios."));
            }
            else
            {
                Servicio::ModificarProductoPedido($estadoPedidoProducto,$tiempoEstimado,$idMesa,$nombreProducto);
                $payload = json_encode(array("mensaje" => "pedido de mesa ".$idMesa." modificado con exito"));
            }
        }

    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    #---------------------------PEDIDOS LISTOS PARA ENTREGAR---------------------------

    public function RecibirPedidosListosParaEntregar($request,$response, $args)
    {
        $params = $request->getQueryParams();

        if(isset($params["tipoPedido"]))
        {
            $tipoPedido = $params["tipoPedido"];
        }
        $estadoPedido = "listo para entregar";

               
        if(empty($estadoPedido))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar pedido, no hay pendientes"));
        }
        else
        {
            $pedido = Pedido::ConsultarPedidoPorEstado($estadoPedido,$tipoPedido);
            
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

    #---------------------------LLEVAR PEDIDOS A MESAS---------------------------

    public function ServirPedido($request,$response, $args)
    {
        $idMesa = $args['idMesa'];
        
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $estadoPedidoProducto = "entregado";
        $tiempoEstimado = 0;
        $nombreProducto = $parametros['nombreProducto'];
        
        if(empty($idMesa))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar la mesa del pedido, campo id vacio."));
        }
        else
        {
        
            Mesa::ModificarMesa("con cliente comiendo",$idMesa);

            Servicio::ModificarProductoPedido($estadoPedidoProducto,$tiempoEstimado,$idMesa,$nombreProducto);
            $payload = json_encode(array("mensaje" => "pedido de mesa ".$idMesa." entregado con exito"));
            
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    #---------------------------COBRAR CLIENTES---------------------------


    public function CobrarPedido($request,$response, $args)
    {
        $idMesa = $args['idMesa'];
        
        
        $estado = "con cliente pagando";
        
        if(empty($idMesa))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar mesa, campo id vacio."));
        }
        else
        {
            Mesa::ModificarMesa($estado,$idMesa);
            $payload = json_encode(array("mensaje" => "Cobrando Mesa"));            
        }

        $pagoExitoso=true;
        
        if($pagoExitoso)
        {
            echo PHP_EOL."pago exitoso";
            Servicio::EliminarPedidoLuegoDeCobrar($idMesa);
        }
        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');          

    }

    #---------------------------CERRAR MESASs---------------------------

    public function CerrarMesa($request,$response, $args)
    {
        $idMesa = $args['idMesa'];
        $estado = "cerrada";
        
        
        if(empty($idMesa))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar mesa, campo id vacio."));
        }
        else
        {
            Mesa::ModificarMesa($estado,$idMesa);
            $payload = json_encode(array("mensaje" => "Mesa cerrada"));            
        }
      

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json'); 
    }
   
}

