<?php

include_once "./clases/Encuesta.php";
include_once "./auxiliar/auxiliar.php";
include_once "./interfaces/IManejadores.php";


/**
 * La clase EncuestaManejador define la entidad encargada de manejar las encuestas realizadas por los clientes
 */
class EncuestaManejador
{
    /**
     * La funcion RealizarEncuesta() se encarga de tomar los datos pasados por parametro por
     * el cliente para subirlo a la tabla 'encuestas' de la base de datos.
     *
     * @param mixed $request
     * @param mixed $response
     * @param mixed $args
     * 
     * @return [type]
     * 
     */
    public function RealizarEncuesta($request,$response, $args)
    {
        $jsonData = file_get_contents('php://input');
        $parametros = json_decode($jsonData,true);
        
        $idMesa = $parametros["idMesa"];
        $idPedido = $parametros["idPedido"];
        $puntajeMesa = $parametros["puntajeMesa"];
        $puntajeMozo = $parametros["puntajeMozo"];
        $puntajeCocinero = $parametros["puntajeCocinero"];
        $puntajeRestaurante = $parametros["puntajeRestaurante"];
        $comentario = $parametros["comentario"];

        Encuesta::AltaEncuesta($puntajeMesa,$puntajeRestaurante,$idMesa,$idPedido,$puntajeMozo,$comentario,$puntajeCocinero);
        $payload = json_encode(array("mensaje" => "Encuesta realizada con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    /**
     * La funcion ObtenerTiempoEspera() se encarga de obtener la informacion de tiempo restante mediante
     * el numero de pedido y numero de mesa.
     *
     * @param mixed $request
     * @param mixed $response
     * @param mixed $args
     * 
     * @return [type]
     * 
     */
    public function ObtenerTiempoEspera($request,$response, $args)
    {
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $idMesa = $parametros['idMesa'];
        $idPedido = $parametros['idPedido'];

               
        if(empty($idMesa) || empty($idPedido))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar pedido, campo id vacio."));
        }
        else
        {
            $pedido = Encuesta::ConsultarTiempoPedido($idMesa,$idPedido);
            
            if($pedido && $pedido->estado != "entregado")
            {

                $diferencia = CalcularDiferenciaTiempoEnMinutos($pedido->registrado,(int)$pedido->estimado);
                
                if($diferencia < 0)
                {
                    $mensaje = "pedido retrasado. Tiempo extra: ". $diferencia;
                }
                else
                {
                    $mensaje = "tiempo restante: ".$diferencia." minutos";
                }
                
                $payload = json_encode(array("pedido"=>$pedido,"mensaje"=>$mensaje));
            }
            else if( $pedido->estado == "entregado")
            {
                $payload = json_encode(array("pedido"=>$pedido,"mensaje" => "Pedido entregado."));
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Pedido no encontrado."));

            }
        }


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    

    
}

