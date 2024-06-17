<?php

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
        $usuario = $parametros['usuarioEmpleado'];
        $contrasena = $parametros['contrasenaEmpleado'];
        $tipo = $parametros['tipoEmpleado'];
    
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

        $usuario = $parametros['usuarioEmpleado'];
        $contrasena = $parametros['contrasenaEmpleado'];
        $tipo = $parametros['tipoEmpleado'];
        
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

    public static function Importar($request, $response, $args)
    {
        $archivo = $_FILES['archivo']['tmp_name'];
        if($archivo)
        {
            $archivo = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $titulosColumnas = true;
            foreach ($archivo as $linea) 
            {
                if(!$titulosColumnas)
                {
                    $columnas = explode(',', $linea);
                    $usuarioNombre = $columnas[1];
                    $usuarioPassword = $columnas[2];
                    $usuarioTipo = $columnas[3];
                    $usuarioIngreso = $columnas[4];
                    
                    Usuario::AltaUsuario($usuarioNombre,$usuarioPassword,$usuarioTipo);
                }
                $titulosColumnas = false;
            }
            $payload = json_encode(array("mensaje" => "Se importaron los usuarios"));
            $response->getBody()->write($payload);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Error: No se importaron los usuarios"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function Exportar($request, $response, $args)
    {
        $fecha = new DateTime(date("d-m-Y"));
        $pathFile = "./archivos";
        $nombreArchivo = 'ListaUsuarios.csv';
        if(!is_dir($pathFile))
        {
            if(!mkdir($pathFile, 0755, true)) 
            {
                die('Error al crear el directorio');
            }
        }
        $rutaCompleta = $pathFile . '/' . $nombreArchivo."_".date_format($fecha, 'Y-m-d H:i:s');

        $archivo = fopen($rutaCompleta, 'w');
        fputcsv($archivo, ['id', 'nombre_usuario', 'contraseña', 'tipo_empleado', 'fecha_ingreso']);

        $usuarios = Usuario::ConsultarTodosLosUsuarios();

        foreach ($usuarios as $usuario) 
        {
            fputcsv($archivo, (array)$usuario);
        }

        fclose($archivo);
        
        $payload = json_encode(array("mensaje"=> "se exporoto el listado de Usuarios"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }

}

