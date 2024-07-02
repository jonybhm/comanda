<?php
// TODO Documentar
use Illuminate\Support\Facades\Log;

include_once "./clases/Pedido.php";
include_once "./clases/Mesa.php";
include_once "./clases/Log.php";
include_once "./clases/PedidoProducto.php";
include_once "./clases/TiempoEntrega.php";
include_once "./auxiliar/auxiliar.php";

class PedidoProductoManejador
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
                if(empty($producto["idProducto"]) || empty($producto["cantidad"]))
                {
                    $payload = json_encode(array("mensaje" => "Error al crear producto, uno o mas campos vacios."));
                }
                else
                {
                    for($i=0;$i<$producto["cantidad"];$i++)
                    {
                        $precioParcial = PedidoProducto::AltaProductosPedido($idPedido,$nombreCliente,$producto["idProducto"],$producto["cantidad"]);                    
                    }

                    $nombre = Producto::ConsultarProducto($producto["idProducto"])->nombre_producto;
                                        
                    $contenidoMensaje .= $nombre." id(".$producto["idProducto"].") X ".$producto["cantidad"]." = $".$precioParcial."||"; 
                }
                    
                $precioTotal = $precioTotal + $precioParcial;
                    
                    
            }
            $payload = json_encode(array("mensaje" => $contenidoMensaje."||TOTAL: $".$precioTotal."||Mesa N°: ".$idMesa."||Pedido N°: ".$idPedido));

            Pedido::ModificarPrecioPedido($precioTotal,$idPedido);
        }

        $usuario = $request->getAttribute('user_data'); 
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario." toma pedidos de clientes" );

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    
    }
    #---------------------------TOMAR FOTO---------------------------

  
    /**
     * @param mixed $request
     * @param mixed $response
     * @param mixed $args
     * 
     * @return [type]
     */
    public function TomarFoto($request,$response, $args)
    {
        $fecha = new DateTime(date("d-m-Y"));
        $rutaArchivo = "./archivos/ImagenesDeLaVenta/2024/";

        if (!is_dir($rutaArchivo)) 
        {
            mkdir($rutaArchivo, 0777, true);
        }

        $parametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();
        $idPedido = $parametros['idPedido'];
        $payload = "";

        if (isset($archivos['foto']) && $idPedido) 
        {

            
            $destinoImagen = $rutaArchivo.$idPedido."_".date_format($fecha, 'Y-m-d_H-i-s').".jpg";
            move_uploaded_file($_FILES["foto"]["tmp_name"],$destinoImagen);
            
            PedidoProducto::AgregarFoto($destinoImagen, $idPedido);
            
            $payload = json_encode(['mensaje' => 'Foto subida.']);
            
        }
        else 
        {
            $payload = json_encode(['mensaje' => 'Error al subir foto, uno o más campos vacíos.']);
        }
        

        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."toma foto de la mesa");
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
         

    }

    #---------------------------LLEGADA PEDIDOS A COCINA/BARRA---------------------------

    public function RecibirPedidosPendientes($request,$response, $args)
    {

        $usuario = $request->getAttribute('user_data');  

        if($usuario)
        {
            $tipoPedido = "";            
            switch($usuario->perfil)
            {
                case "cocinero":
                    $tipoPedido = "comida";
                    break;
                case "bartender":
                    $tipoPedido = "bebida";
                    break; 
                case "cervecero":
                    $tipoPedido = "cerveza";
                    break;                                        
            }
        }               
    
        $pedido = Pedido::ConsultarPedidoPorEstado("pendiente",$tipoPedido);
        
        if($pedido)
        {
            $payload = json_encode($pedido);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Sin pedidos."));
        }        

        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."recibe pedidos pendientes" );

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    }


    #---------------------------PENDIENTES PEDIDOS A COCINA/BARRA---------------------------

    public function RecibirPedidosEnPreparacionCocina($request,$response, $args)
    {

        $usuario = $request->getAttribute('user_data');  

        if($usuario)
        {
            $tipoPedido = "";            
            switch($usuario->perfil)
            {
                case "cocinero":
                    $tipoPedido = "comida";
                    break;
                case "bartender":
                    $tipoPedido = "bebida";
                    break; 
                case "cervecero":
                    $tipoPedido = "cerveza";
                    break;                                        
            }
        }               
    
        $pedido = Pedido::ConsultarPedidoPorEstado("en preparacion",$tipoPedido);
        
        if($pedido)
        {
            $payload = json_encode($pedido);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Sin pedidos."));
        }        

        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."recibe pedidos pendientes" );

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    }

    

    #---------------------------CAMBIAR ESTADO DE LOS PEDIDOS---------------------------


    public function ModificarPedidosPendientes($request,$response, $args)
    {
        $usuario = $request->getAttribute('user_data');  

        if($usuario)
        {
            $tipoPedido = "";            
            switch($usuario->perfil)
            {
                case "cocinero":
                    $tipoPedido = "comida";
                    break;
                case "bartender":
                    $tipoPedido = "bebida";
                    break; 
                case "cervecero":
                    $tipoPedido = "cerveza";
                    break;                                        
            }
        }               
    
       
        $idPedidoProducto = $args['idPedidoProducto'];

        $tipoProducto = PedidoProducto::ConsultarTipoProducto($idPedidoProducto)->tipo_producto;
        
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $estadoPedidoProducto = $parametros['estadoPedidoProducto'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        
        if($tipoProducto == $tipoPedido)
        {
            if(empty($idPedidoProducto))
            {
                $payload = json_encode(array("mensaje" => "Error al buscar la mesa del pedido, campo id vacio."));
            }
            else
            {
                if(empty($estadoPedidoProducto) || empty($tiempoEstimado))
                {
                    $payload = json_encode(array("mensaje" => "Error al modificar pedido ".$idPedidoProducto.", uno o mas campos vacios."));
                }
                else
                {
                    PedidoProducto::ModificarProductoPedido($estadoPedidoProducto,$tiempoEstimado,$idPedidoProducto);
                    $payload = json_encode(array("mensaje" => "Estado del pedido ".$idPedidoProducto." modificado con exito a: '".$estadoPedidoProducto."'"));
                }
            }
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No puede modificar pedidos de otro sector."));
        }

        //VERIFICAR que AL MENOS UN ELEMENTO ESTE EN PREPARACION 
        $pedido = Pedido::ConsultarIdPedidoPorIdProductoPedido($idPedidoProducto);
        $estadoFinal = PedidoProductoManejador::VerificarEstadoPedido($pedido->id_pedido);
        Pedido::ModificarEstadoPedido($estadoFinal,$pedido->id_pedido);
        $tiempoFinal = PedidoProductoManejador::VerificarTiempoFinal($pedido->id_pedido);
        Pedido::ModificarTiempoPedido($tiempoFinal,$pedido->id_pedido);

        echo PHP_EOL.$estadoFinal." ".$tiempoFinal.PHP_EOL;

        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);     

        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."cambio el estado de pedido a: ".$estadoFinal );

        //CREAR NUEVO TEIMPO ESPERA
        if($estadoPedidoProducto == "en preparacion")
        {
            TiempoEspera::AltaTiempoEspera($idPedidoProducto,$tiempoEstimado,$pedido->id_pedido);
        }
        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

     #---------------------------PEDIDOS PENDIENTES MOZOS---------------------------

    public function RecibirPedidosPendientesMozo($request,$response, $args)
    {

        $arrayTipoPedidos = ["comida","bebida","cerveza"];
        $arrayMensaje = array();

        foreach($arrayTipoPedidos as $tipo)
        {
            
            $pedido = Pedido::ConsultarPedidoPorEstado("pendiente",$tipo);
            
            if($pedido)
            {            
                array_push($arrayMensaje,$pedido);
            }  
            else
            {
                array_push($arrayMensaje, "No hay pedidos del tipo ".$tipo." que estén pendientes");
            }
        }
        
        $payload = json_encode($arrayMensaje);
        
        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."recibe pedidos pendientes" );

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    }


    #---------------------------PEDIDOS EN PREPARACION---------------------------

    public function RecibirPedidosEnPreparacion($request,$response, $args)
    {

        $arrayTipoPedidos = ["comida","bebida","cerveza"];
        $arrayMensaje = array();

        foreach($arrayTipoPedidos as $tipo)
        {
            
            $pedido = Pedido::ConsultarPedidoPorEstado("en preparacion",$tipo);
            
            if($pedido)
            {            
                array_push($arrayMensaje,$pedido);
            }  
            else
            {
                array_push($arrayMensaje, "No hay pedidos del tipo ".$tipo." que estén en preparacion");
            }
        }
        
        $payload = json_encode($arrayMensaje);
        
        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."recibe pedidos en preparacion" );

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    }

    #---------------------------PEDIDOS LISTOS PARA ENTREGAR---------------------------

    public function RecibirPedidosListosParaEntregar($request,$response, $args)
    {

        $arrayTipoPedidos = ["comida","bebida","cerveza"];
        $arrayMensaje = array();

        foreach($arrayTipoPedidos as $tipo)
        {
            
            $pedido = Pedido::ConsultarPedidoPorEstado("listo para entregar",$tipo);
            
            if($pedido)
            {            
                array_push($arrayMensaje,$pedido);
            }  
            else
            {
                array_push($arrayMensaje, "No hay pedidos del tipo ".$tipo." que estén listos para entregar");
            }
        }
        
        $payload = json_encode($arrayMensaje);
        
        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."recibe pedidos listos para entregar" );

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    }

    #---------------------------PEDIDOS ENTREGADOS---------------------------

    public function RecibirPedidosEntregados($request,$response, $args)
    {

        $arrayTipoPedidos = ["comida","bebida","cerveza"];
        $arrayMensaje = array();

        foreach($arrayTipoPedidos as $tipo)
        {
            
            $pedido = Pedido::ConsultarPedidoPorEstado("entregado",$tipo);
            
            if($pedido)
            {            
                array_push($arrayMensaje,$pedido);
            }  
            else
            {
                array_push($arrayMensaje, "No hay pedidos del tipo ".$tipo." que estén entregados");
            }
        }
        
        $payload = json_encode($arrayMensaje);
        
        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."recibe pedidos entregados" );

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    }

    #---------------------------LLEVAR PEDIDOS A MESAS---------------------------

    public function ServirPedido($request,$response, $args)
    {
        $idPedidoProducto = $args['idPedidoProducto'];
        
        
        
        if(empty($idPedidoProducto))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar el pedido, campo id vacio."));
        }
        else
        {
        
            PedidoProducto::ModificarProductoPedido("entregado",0,$idPedidoProducto);
            $payload = json_encode(array("mensaje" => "pedido ".$idPedidoProducto." entregado con exito"));
            
        }

        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        $logId = LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."entrega pedidos en mesa" );  

        #Verificar que todos los elementos del pedido esten entregados
        $pedido = Pedido::ConsultarIdPedidoPorIdProductoPedido($idPedidoProducto);  
        $estadoFinal = PedidoProductoManejador::VerificarEstadoPedido($pedido->id_pedido);
        Pedido::ModificarEstadoPedido($estadoFinal,$pedido->id_pedido);

        //MODIFICAR TIEMPOESPERA
        $HoraPedidoEnPreparacion = date("H:i:s");

        TiempoEspera::ModificarTiempoEsperaFinal($HoraPedidoEnPreparacion,$idPedidoProducto);
        TiempoEspera::ModificarTiempoEsperaAtrasado();

        //CAMBIAR ESTADO MESA
        if($estadoFinal == "entregado")
        {
            $idMesa = Pedido::ConsultarIdMesaPorIdPedido($pedido->id_pedido);
            //var_dump($idMesa);
            Mesa::ModificarMesa("con cliente comiendo",$idMesa->id_mesa);
        }      

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');    
    
    }


    #---------------------------CANCELAR PEDIDOS---------------------------


    public function CancelarPedido($request,$response, $args)
    {
        $idPedido = $args['idPedido'];       
        
        
        if(empty($idPedido))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar el pedido, campo id vacio."));
        }
        else
        {
            Pedido::ModificarEstadoPedido("cancelado",$idPedido);
            PedidoProducto::CancelarProductoPedido("cancelado",$idPedido);
            $payload = json_encode(array("mensaje" => "pedido ".$idPedido." cancelado con exito"));
            
        }

        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        $logId = LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario."cancelo pedidos" );  


        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');    
    
    }


    #---------------------------COBRAR CLIENTES---------------------------


    public function CobrarPedido($request,$response, $args)
    {
        $idPedido = $args['idPedido'];
        
        if(empty($idPedido))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar mesa, campo id vacio."));
        }
        else
        {
            $pedidoMesa = Pedido::ConsultarIdMesaPorIdPedido($idPedido);
            Mesa::ModificarMesa("con cliente pagando",$pedidoMesa->id_mesa);
            $payload = json_encode(array("mensaje" => "Cobrando Mesa"));            
        }

        $pagoExitoso=true;
        
        if($pagoExitoso)
        {
            Pedido::ModificarEstadoPedido("cobrado",$idPedido);
        }

        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario." cobro a los clientes en mesa ".$idPedido);

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');          

    }

    #---------------------------CERRAR MESASs---------------------------

    public function CerrarMesa($request,$response, $args)
    {
        $idMesa = $args['idMesa'];
   
        if(empty($idMesa))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar mesa, campo id vacio."));
        }
        else
        {
            Mesa::ModificarMesa("cerrada",$idMesa);
            $payload = json_encode(array("mensaje" => "Mesa cerrada"));            
        }
      
        $usuario = $request->getAttribute('user_data');         
        $idUsuario = Usuario::ConsultarUsuarioPorNombre($usuario->usuario);       
        LogUsuario::registrarLog($idUsuario->id, "El usuario ".$usuario->usuario." cerró la mesa." );

        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json'); 
    }

    #---------------------------VERIFICACIONES---------------------------

   
    static public function VerificarEstadoPedido($idPedidoProducto)
    {
        $pedidos = PedidoProducto::TraerTodosLosPedidosProductos($idPedidoProducto);
        $contadorPreparcion = 0;
        $contadorListos = 0;
        $contadorEntregado = 0;
        $estadoFinal = "";
        
        foreach($pedidos as $pedido)
        {
            if ($pedido->estado_producto == 'en preparacion')
            {
                $contadorPreparcion++;
                $estadoFinal = "en preparacion";
            }
            else if($pedido->estado_producto == "listo para entregar")
            {
                $contadorListos++;
                
                if($contadorListos==count($pedidos) || ($contadorListos>0 && $contadorEntregado>0 && $contadorPreparcion==0))
                {
                    $estadoFinal = "listo para entregar";
                }
            }
            else if($pedido->estado_producto == "entregado")
            {
                $contadorEntregado++;

                if($contadorEntregado==count($pedidos))
                {
                    $estadoFinal = "entregado";
                }
            }
        }

        return $estadoFinal;
        
    }

    static public function VerificarTiempoFinal($idPedidoProducto)
    {
        $pedidos = PedidoProducto::TraerTodosLosPedidosProductos($idPedidoProducto);
        $tiempoFlag = 0;
        foreach($pedidos as $pedido)
        {
            if($pedido->tiempo_estimado > $tiempoFlag)
            {
                $tiempoFlag = $pedido->tiempo_estimado;
            }
        }

        return $tiempoFlag;
        
    }
}

