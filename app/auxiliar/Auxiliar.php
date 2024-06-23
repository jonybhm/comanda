<?php

use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;
use Slim\Psr7\Response as ResponseClass;


function GenerarClaveAlfaNumerica()
{
    //Armo un hash completo
    $hashCompleto = password_hash(rand(), PASSWORD_DEFAULT);
    //Dejo solo los valores alfanumericos
    $claveAlfaNumerica = preg_replace("/[^0-9A-Z]/", "", $hashCompleto);
    //Reduzo a 5 caracteres
    return $clave = substr($claveAlfaNumerica,-5);
}

function CalcularDiferenciaTiempoEnMinutos($timestampPrevio,$tiempoEstimado)
{
    $timestampActual = date("H:i:s");

    //tomo la parte de minutos y lo convierto a int
    $minutosActual = (int)explode(":",$timestampActual)[1];
    $minutosPrevio = (int)explode(":",$timestampPrevio)[1];

    //calculo la diferencia
    $diferenciaMinutos = $minutosActual - $minutosPrevio;
    
    if($diferenciaMinutos>$tiempoEstimado)
    {
        return $minutosFaltantes = 0;
    }
    else
    {
        return $minutosFaltantes = $tiempoEstimado-$diferenciaMinutos;
    } 
    
}
