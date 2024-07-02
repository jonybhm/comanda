<?php
require_once '../vendor/autoload.php';


use Firebase\JWT\JWT;

/*
La clase JasonWebToken representa la entidad de tokens JWT, contiene metodos para Crear, Verificar y Obtener tokens.
 */
class JasonWebToken
{
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = ['HS256'];
    
    /**
     * La funcion CrearToken() permite crear un jwt mediante los datos pasados
     *
     * @param mixed $datos
     * 
     * @return [type]
     * 
     */
    public static function CrearToken($datos)
    {
        $tiempoActual = time();
    
        $payload = array(
            'iat' => $tiempoActual,
            'exp' => $tiempoActual + (60000),
            'aud' => self::ObtenerDatosIpParaAudiencia(),
            'data' => $datos,
            'app' => "Test JWT"
        );
    
        return JWT::encode($payload, self::$claveSecreta);
    }
    
    /**
     * La funcion VerificarToken() recibe un token y lo decodifica, en caso de encontrar un error tira una excpecion
     *
     * @param mixed $token
     * 
     * @return [type]
     * 
     */
    public static function VerificarToken($token)
    {
        if (empty($token)) 
        {
            throw new Exception("El token esta vacio.");
        }
        try 
        {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } 
        catch (Exception $e) 
        {
            throw $e;
        }
        if ($decodificado->aud !== self::ObtenerDatosIpParaAudiencia()) 
        {
            throw new Exception("No es el usuario valido");
        }
    }
    
    /**
     * La funcion ObtenerPayLoad() recibe un token y devuelve el contenido del mismo
     *
     * @param mixed $token
     * 
     * @return [type]
     * 
     */
    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }
    
    /**
     * La funcion ObtenerData() recibe un token y devuelve los datos contenidos dentro del mismo
     *
     * @param mixed $token
     * 
     * @return [type]
     * 
     */
    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }
    
    /**
     * La funcion ObtenerDatosIpParaAudiencia() obtiene datos de la IP para la audiencia a la hora de generar un token
     *
     * @return [type]
     * 
     */
    public static function ObtenerDatosIpParaAudiencia()
    {
        $aud = '';
    
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else 
        {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
    
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
    
        return sha1($aud);
    }
}
