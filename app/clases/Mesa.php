<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

class Mesa
{
    private $_id;
    private $_estado;

    public function __construct($id = NULL, $estado = NULL)
    {
        $this->_id = $id;
        $this->_estado = $estado;        
    }
    
    static public function AltaMesa()
    {
        $claveAlfaNumerica = GenerarClaveAlfaNumerica();
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO mesas (id, estado) VALUES (?, 'cerrada')";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1,$claveAlfaNumerica , PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }

        return $claveAlfaNumerica;
    }
    
    static public function ConsultarMesa($id)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM mesas WHERE id = ?";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $id, PDO::PARAM_STR);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Mesa');
        $elemento = $consulta -> fetch();
        return $elemento;
    }
    
    static public function ConsultarTodasLasMesas()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM mesas";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }

    static public function ModificarMesa($estado,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE mesas SET estado = ? WHERE id = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $estado, PDO::PARAM_STR);
            $consulta -> bindValue(2, $id, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }
    }

    static public function BorrarMesa($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM mesas WHERE id = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $id, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al elimiar elemento: ".$e->getMessage();
        }
    }
} 





