<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";
date_default_timezone_set('America/Argentina/Buenos_Aires');

class Estadistica
{
    private $_id;
    
    public function __construct($id = NULL)
    {
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


    public static function RegistrarLog($idUsuario,$accionTomada)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO log_usuarios (id_usuario, accion_tomada) VALUES (?,?)";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idUsuario, PDO::PARAM_STR);
            $consulta -> bindValue(2, $accionTomada, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }
    }
    
    
} 





