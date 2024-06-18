<?php

include_once "./token/JasonWebToken.php";
include_once "./clases/Usuario.php";
include_once "./auxiliar/auxiliar.php";

class TokenManejador
{
    public function IngresarYGenerarToken($request,$response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombreUsuario = $parametros['usuarioEmpleado'];
        $contraseña = $parametros['contrasenaEmpleado'];
       

        $usuario = Usuario::VerificarUsuarioYContraseña($nombreUsuario,$contraseña);
        
        if($usuario)
        {  

            $datos = array('usuario' => "",'perfil' => "");
            
            foreach($usuario as $key => $value)
            {
                switch($key)
                {
                    case "nombre_usuario":
                        $datos['usuario'] = $value;
                        break;   
                    case "tipo_empleado":
                        $datos['perfil'] = $value;
                        break;                                       
                }
            }   
        
            $token = JasonWebToken::CrearToken($datos);
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


