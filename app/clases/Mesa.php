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
        $query = "INSERT INTO mesas (id, estado_mesa) VALUES (?, 'cerrada')";
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

    static public function ConsultarMesaMayorImporte()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, pedidos.precio_total AS importe FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa ORDER BY importe DESC LIMIT 1";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }
    
    static public function ConsultarMesaMenorImporte()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, pedidos.precio_total AS importe FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa ORDER BY importe ASC LIMIT 1";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }

    static public function ConsultarMesasPorOrdenDeFacturacionDescendente()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, SUM(pedidos.precio_total) AS facturado FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa GROUP BY mesas.id ORDER BY facturado DESC";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }

    static public function ConsultarMesasPorOrdenDeFacturacionAscendente()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, SUM(pedidos.precio_total) AS facturado FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa GROUP BY mesas.id ORDER BY facturado ASC";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }


    static public function ConsultarFacturacionMesasEntreFechas($fechaMin,$fechaMax,$idMesa)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, SUM(pedidos.precio_total) AS facturado FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa WHERE mesas.id = ? AND fecha BETWEEN ? AND ?";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $idMesa, PDO::PARAM_STR);
        $consulta -> bindValue(2, $fechaMin, PDO::PARAM_STR);
        $consulta -> bindValue(3, $fechaMax, PDO::PARAM_STR);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }

    static public function ModificarMesa($estado,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE mesas SET estado_mesa = ? WHERE id = ?";
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





