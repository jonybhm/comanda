<?php


include_once "./token/JasonWebToken.php";
include_once "./clases/Usuario.php";
include_once "./clases/Log.php";
include_once "./auxiliar/auxiliar.php";

/*La clase TokenManejador representa la entidad dedicada a manejar tokens generados mediante JWT
 */
class TokenManejador
{
    /**
     * La funcoin IngresarYGenerarToken() recibe la informacion del usuario mediante parametros y luego de verificar
     * su validez utiliza dichos valores para generar un token
     *
     * @param mixed $request
     * @param mixed $response
     * @param mixed $args
     * 
     * @return [type]
     * 
     */
    public function IngresarYGenerarToken($request,$response, $args)
    {
        $jsonData = file_get_contents('php://input');
        $parametros = json_decode($jsonData,true);

        $nombreUsuario = $parametros['usuarioEmpleado'];
        $contraseña = $parametros['contrasenaEmpleado'];
       

        $usuario = Usuario::VerificarUsuarioYContraseña($nombreUsuario,$contraseña);
        
        if($usuario)
        {  

            $datos = array('usuario' => $usuario->nombre_usuario,'perfil' => $usuario->tipo_empleado);
            
                   
            $token = JasonWebToken::CrearToken($datos);
            $payload = json_encode(array('jwt' => $token));
        } 
        else
        {
            $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
        }
    

        LogUsuario::RegistrarLog($usuario->id,"El usuario ".$usuario->nombre_usuario." ha iniciado sesion.");

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }
}


