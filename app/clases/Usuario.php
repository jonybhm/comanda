<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

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
        CrearElemento($pdo,$query,$nombreUsuario,$password,$tipo);
    }
    
    static public function ConsultarUsuario($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios WHERE id = ?";
        return ObtenerElemento($pdo,$id,'Usuario',$query);
    }
    
    static public function ConsultarTodosLosUsuarios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios";
        return ObtenerTodosLosElementos($pdo,'usuarios','Usuario',$query);        
    }

    static public function ModificarUsuario($nombreUsuario,$password,$tipo,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE usuarios SET nombre_usuario = ?, contraseña = ?, tipo_empleado = ? WHERE id = ?";
        ModificarElemento($pdo,$query,$nombreUsuario,$password,$tipo,$id);
    }

    static public function BorrarUsuario($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM usuarios WHERE id = ?";
        BorrarElemento($pdo,$query,$id);
    }
} 





