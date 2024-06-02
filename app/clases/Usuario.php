<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

class Usuario
{
    private $_nombreUsuario;
    private $_password;
    private $_id;

    public function __construct($nombreUsuario = NULL,$password = NULL,$id = NULL)
    {
        $this->_nombreUsuario = $nombreUsuario;
        $this->_password = $password;
        $this->_id = $id;        
    }
    
    static public function AltaUsuario($nombreUsuario,$password)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO usuarios (nombre, contraseña) VALUES (?,?)";
        CrearElemento($pdo,$query,$nombreUsuario,$password);
    }
    
    static public function ConsultarUsuario($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT id, nombre, contraseña FROM usuarios WHERE id = ?";
        return ObtenerElemento($pdo,$id,'Usuario',$query);
    }
    
    static public function ConsultarTodosLosUsuarios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios";
        return ObtenerTodosLosElementos($pdo,'usuarios','Usuario',$query);        
    }

    static public function ModificarUsuario($nombreUsuario,$password,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE usuarios SET nombre = ?, contraseña = ? WHERE id = ?";
        ModificarElemento($pdo,$query,$nombreUsuario,$password,$id);
    }

    static public function BorrarUsuario($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM usuarios WHERE id = ?";
        BorrarElemento($pdo,$query,$id);
    }
} 





