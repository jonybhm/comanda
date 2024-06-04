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

    public function __construct($id = NULL, $nombreUsuario = NULL, $password = NULL, $tipoEmpleado = NULL, $fechaIngreso = NULL)
    {
        $this->_nombreUsuario = $nombreUsuario;
        $this->_password = $password;
        $this->_id = $id;        
        $this->_tipoEmpleado = $tipoEmpleado;        
        $this->_fechaIngreso = $fechaIngreso;        

    }
    
    static public function AltaUsuario($nombreUsuario,$password,$tipo)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO usuarios (nombre_usuario, contraseña, tipo_empleado, fecha_ingreso) VALUES (?,?,?,?)";

        $fecha = new DateTime(date("d-m-Y"));

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_STR);
            $consulta -> bindValue(2, password_hash($password,PASSWORD_DEFAULT));
            $consulta -> bindValue(3, $tipo, PDO::PARAM_STR);
            $consulta -> bindValue(4, date_format($fecha, 'Y-m-d H:i:s'), PDO::PARAM_STR);
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
    
    static public function ConsultarTodosLosUsuarios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios";
        
        $consulta = $pdo->prepare($query);
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
} 





