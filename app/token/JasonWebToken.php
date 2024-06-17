<?php
require_once '../vendor/autoload.php';


use Firebase\JWT\JWT;

 

    function CrearToken($datos,$claveSecreta)
    {
        $tiempoActual = time();

        $payload = array(
            'iat' => $tiempoActual,
            'exp' => $tiempoActual + (60000),
            'aud' => ObtenerDatosIpParaAudiencia(),
            'data' => $datos,
            'app' => "Test JWT"
        );

        return JWT::encode($payload, $claveSecreta);
    }

    function VerificarToken($token,$claveSecreta,$tipoEncriptacion)
    {
        if (empty($token)) 
        {
            throw new Exception("El token esta vacio.");
        }
        try 
        {
            $decodificado = JWT::decode(
                $token,
                $claveSecreta,
                $tipoEncriptacion
            );
        } 
        catch (Exception $e) 
        {
            throw $e;
        }
        if ($decodificado->aud !== ObtenerDatosIpParaAudiencia()) 
        {
            throw new Exception("No es el usuario valido");
        }
    }

    function ObtenerPayLoad($token,$claveSecreta,$tipoEncriptacion)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            $claveSecreta,
            $tipoEncriptacion
        );
    }

    function ObtenerData($token,$claveSecreta,$tipoEncriptacion)
    {
        return JWT::decode(
            $token,
            $claveSecreta,
            $tipoEncriptacion
        )->data;
    }
    
    function ObtenerDatosIpParaAudiencia()
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