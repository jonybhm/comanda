<?php

// TODO Documentar

include_once "./clases/usuario.php";
include_once "./auxiliar/auxiliar.php";
include_once "./interfaces/IManejadores.php";
date_default_timezone_set('America/Argentina/Buenos_Aires');


class UsuarioManejador implements IManejadores
{
    public function Alta($request,$response, $args)
    {
        $parametros = $request->getParsedBody();

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $usuario = $parametros['usuario'];
        $contrasena = $parametros['contraseña'];
        $tipo = $parametros['sector'];
    
        if(empty($usuario) || empty($contrasena))
        {
            $payload = json_encode(array("mensaje" => "Error al crear usuario, uno o mas campos vacios."));
        }
        else
        {
            Usuario::AltaUsuario($usuario,$contrasena,$tipo);
            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        }


        $response->getBody()->write($payload);
        
        return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerUno($request,$response, $args)
    {
        $id = (int)$args['id'];

        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar usuario, campo id vacio."));
        }
        else
        {
            $usuario = Usuario::ConsultarUsuario($id);
            
            if($usuario)
            {
                $payload = json_encode($usuario);
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Usuario no encontrado."));
            }
        }


    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function ObtenerTodos($request,$response, $args)
    {
        $listaUsuarios = Usuario::ConsultarTodosLosUsuarios();

        if(empty($listaUsuarios))
        {
            $payload = json_encode(array("mensaje" => "No hay usuarios"));
        }
        else
        {            
            $payload = json_encode($listaUsuarios);        
        }


    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function Modificar($request,$response, $args)
    {
        $id = (int)$args['id'];
        
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $usuario = $parametros['usuario'];
        $contrasena = $parametros['contraseña'];
        $tipo = $parametros['sector'];
        
        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar usuario, campo id vacio."));
        }
        else
        {
            if(empty($usuario) || empty($contrasena) || empty($tipo))
            {
                $payload = json_encode(array("mensaje" => "Error al modificar usuario, uno o mas campos vacios."));
            }
            else
            {
                Usuario::ModificarUsuario($usuario,$contrasena,$tipo,$id);
                $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
            }
        }

    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    public function Baja($request,$response, $args)
    {
        $parametros = $request->getParsedBody();    

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $id = $parametros['id'];
        $estadoNuevo = $parametros['estadoNuevo'];

        if(empty($id) || empty($estadoNuevo))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar usuario, campo id vacio."));
        }
        else
        {
            Usuario::BorradoOSuspencionUsuario($estadoNuevo,$id);
            $payload = json_encode(array("mensaje" => "El usuario se ha ".$estadoNuevo." con exito"));

        }

    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }

    
}

