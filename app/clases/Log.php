
<?php

class LogUsuario
{
    private $_id;
    private $_idUsuario;
    private $_accionTomada;
    private $_fechaHora;

    public function __construct($id = NULL, $idUsuario = NULL, $accionTomada = NULL, $fechaHora = NULL)
    {
        $this->_idUsuario = $idUsuario;
        $this->_accionTomada = $accionTomada;
        $this->_id = $id;        
        $this->_fechaHora = $fechaHora;        

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

            $lastInsertId = $pdo->lastInsertId();
            return $lastInsertId;
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }
    }

    static public function ObtenerLog($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM log_usuarios WHERE id = ?";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $id, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'LogUsuario');
        $elemento = $consulta -> fetch();
        return $elemento;
    }

    static public function ObtenerTodosLosLogs()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM log_usuarios";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'LogUsuario');
    }
}