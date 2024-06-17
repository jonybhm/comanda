<?php

include_once "./token/JasonWebToken.php";
include_once "./clases/Usuario.php";
include_once "./auxiliar/auxiliar.php";

class TokenManejador
{
    public function IngresarYGenerarToken($request,$response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombreUsuario = $parametros['usuario'];
        $contraseña = $parametros['contraseña'];
        //$credenciales = $parametros['credenciales'];

        $claveSecreta = 'T3sT$JWT';
        $tipoEncriptacion = ['HS256']; 

        $usuario = Usuario::VerificarUsuarioYContraseña($nombreUsuario,$contraseña);
        
        if($usuario)
        {  
            var_dump($usuario);
            $datos = array('usuario' => $usuario["nombre"],'perfil' => $usuario["tipo_empleado"]);
        
            $token = CrearToken($datos,$claveSecreta,$tipoEncriptacion);
            $payload = json_encode(array('jwt' => $token));
        } 
        else
        {
            $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }
}


