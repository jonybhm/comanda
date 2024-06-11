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

