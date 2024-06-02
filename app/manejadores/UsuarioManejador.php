<?php

include_once "./clases/usuario.php";
include_once "./auxiliar/auxiliar.php";
include_once "./interfaces/IManejadores.php";

class UsuarioManejador implements IManejadores
{
    public function Alta($request,$response, $args)
    {
        $parametros = $request->getParsedBody();
        var_dump($parametros);
        $usuario = $parametros['usuario'];
        $contrasena = $parametros['contrasena'];
        $tipo = $parametros['tipo_empleado'];
    
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
        var_dump($parametros);

        if (!$parametros) 
        {
            $payload = json_encode(array("mensaje" => "No se recibieron los datos correctamente."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $usuario = $parametros['usuario'];
        $contrasena = $parametros['contrasena'];
        $tipo = $parametros['tipo_empleado'];
        
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
        $id = (int)$args['id'];
        
        if(empty($id))
        {
            $payload = json_encode(array("mensaje" => "Error al buscar usuario, campo id vacio."));
        }
        else
        {
            Usuario::BorrarUsuario($id);
            $payload = json_encode(array("mensaje" => "El usuario se ha borrado con exito"));

        }

    $response->getBody()->write($payload);
    
    return $response->withHeader('Content-Type', 'application/json');    
    
    }
}

