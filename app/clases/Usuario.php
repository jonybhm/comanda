<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";
date_default_timezone_set('America/Argentina/Buenos_Aires');

class Usuario
{
    private $_id;
    private $_nombreUsuario;
    private $_password;
    private $_tipoEmpleado;
    private $_fechaIngreso;
    private $_estado;

    public function __construct($id = NULL, $nombreUsuario = NULL, $estado = NULL, $password = NULL, $tipoEmpleado = NULL, $fechaIngreso = NULL)
    {
        $this->_nombreUsuario = $nombreUsuario;
        $this->_password = $password;
        $this->_id = $id;        
        $this->_tipoEmpleado = $tipoEmpleado;        
        $this->_fechaIngreso = $fechaIngreso;        
        $this->_estado = $estado;        
    }

    static public function AltaUsuario($nombreUsuario,$password,$tipo)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO usuarios (nombre_usuario, contraseña, tipo_empleado, estado) VALUES (?,?,?,?)";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_STR);
            $consulta -> bindValue(2, password_hash($password,PASSWORD_DEFAULT));
            $consulta -> bindValue(3, $tipo, PDO::PARAM_STR);
            $consulta -> bindValue(4, "Activo", PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }

    }
    
    static public function ConsultarUsuario($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios WHERE id = ?";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $id, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Usuario');
        $elemento = $consulta -> fetch();
        return $elemento;
    }

    static public function ConsultarUsuarioPorNombre($nombreUsuario)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Usuario');
        $elemento = $consulta -> fetch();
        return $elemento;
    }
    
    
    static public function ConsultarTodosLosUsuarios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    static public function ConsultarTodosLosEstadosDeUsuarios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT nombre_usuario,estado FROM usuarios";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    static public function ConsultarCantidadOPeracionesUsuarioSector($tipoEmpleado)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT usuarios.tipo_empleado, COUNT(log_usuarios.id_usuario) AS cantidad_operaciones FROM log_usuarios INNER JOIN usuarios ON log_usuarios.id_usuario = usuarios.id WHERE usuarios.tipo_empleado = ? GROUP BY usuarios.tipo_empleado DESC ";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $tipoEmpleado, PDO::PARAM_INT);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    static public function ConsultarCantidadOPeracionesUsuarioNombre($tipoEmpleado,$nombreUsuario)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT usuarios.nombre_usuario, COUNT(log_usuarios.id_usuario) AS cantidad_operaciones FROM log_usuarios INNER JOIN usuarios ON log_usuarios.id_usuario = usuarios.id WHERE usuarios.tipo_empleado = ? AND usuarios.nombre_usuario=? GROUP BY usuarios.tipo_empleado ORDER BY cantidad_operaciones DESC ";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $tipoEmpleado, PDO::PARAM_INT);
        $consulta -> bindValue(2, $nombreUsuario, PDO::PARAM_STR);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    static public function ConsultarCantidadOPeracionesUsuarioSectoryNombre($tipoEmpleado)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT usuarios.nombre_usuario, COUNT(log_usuarios.id_usuario) AS cantidad_operaciones FROM log_usuarios INNER JOIN usuarios ON log_usuarios.id_usuario = usuarios.id WHERE usuarios.tipo_empleado = ? GROUP BY usuarios.nombre_usuario ORDER BY cantidad_operaciones DESC ";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $tipoEmpleado, PDO::PARAM_INT);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    static public function ConsultarLogeoUsuario($nombreUsuario)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT usuarios.nombre_usuario, log_usuarios.accion_tomada, COUNT(log_usuarios.id_usuario) AS cantidad FROM log_usuarios INNER JOIN usuarios ON log_usuarios.id_usuario = usuarios.id WHERE usuarios.nombre_usuario=? AND accion_tomada LIKE '%sesion%' ";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_INT);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    static public function ModificarUsuario($nombreUsuario,$password,$tipo,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE usuarios SET nombre_usuario = ?, contraseña = ?, tipo_empleado = ? WHERE id = ?";
        
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_STR);
            $consulta -> bindValue(2, password_hash($password,PASSWORD_DEFAULT));
            $consulta -> bindValue(3, $tipo, PDO::PARAM_STR);
            $consulta -> bindValue(4, $id, PDO::PARAM_INT);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }

    static public function BorradoOSuspencionUsuario($nuevoEstado,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE usuarios SET estado = ? WHERE id = ?";
        
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nuevoEstado, PDO::PARAM_STR);
            $consulta -> bindValue(2, $id, PDO::PARAM_INT);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }



    static public function BorrarUsuario($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM usuarios WHERE id = ?";
        
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $id, PDO::PARAM_INT);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al elimiar elemento: ".$e->getMessage();
        }
    }

    static public function VerificarUsuarioYContraseña($nombreUsuario,$password)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        try
        {   
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_INT);
            $consulta -> execute();

            $consulta -> setFetchMode(PDO::FETCH_CLASS,'Usuario');
            $usuario = $consulta -> fetch();


            foreach($usuario as $key => $value)
            {
                switch($key)
                {
                    case "contraseña":
                        $hash = $value;
                        break;                                       
                }
            }   
            

            $contrasenaValida = password_verify($password,$hash);
            

            if($usuario && $contrasenaValida)
            {
                return $usuario;
            }
            else
            {
                return NULL;
            }
        }
        catch(PDOException $e)
        {
            echo "Error al buscar usuario: ".$e->getMessage();
        }
    }  
    
} 





