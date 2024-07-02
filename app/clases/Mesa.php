<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

/* La clase `Mesa` en PHP contiene métodos estáticos para interactuar con una tabla de base de datos
que representa mesas en un restaurante, incluidas operaciones como crear, consultar, actualizar y
eliminar registros de la tabla. */
class Mesa
{
    private $_id;
    private $_estado;

    /**
     *
     * @param null $id
     * @param null $estado
     * 
     */
    public function __construct($id = NULL, $estado = NULL)
    {
        $this->_id = $id;
        $this->_estado = $estado;        
    }
    
    /**
     * 
     * La `AltaMesa()` en la clase `Mesa` es un método estático utilizado para
     * crear un nuevo registro en la tabla `mesas` de la base de datos.
     * 
     * @return [type]
     * 
     */
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
    
    /**
     * 
     * El método `ConsultarMesa()` en la clase `Mesa` se utiliza para
     * recuperar un registro específico de la tabla `mesas` en la base de datos según el `id`
     * proporcionado.
     *  
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
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
    
    
    /**
     * 
     * La función `ConsultarTodasLasMesas()` en la clase `Mesa` es un método público estático utilizado
     * para recuperar todos los registros de la tabla `mesas` en la base de datos.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarTodasLasMesas()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM mesas";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }


    /**
     * La función `ConsultarMesaMayorImporte()` en la clase `Mesa` está consultando la base de datos
     * para encontrar la tabla `mesas` con el precio total más alto (`precio_total`) de pedidos
     * (`pedidos`) dentro de los últimos 30 días. 
     * 
     * @return [type]
     * 
     */
    
    static public function ConsultarMesaMayorImporte()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, pedidos.precio_total AS importe FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY importe DESC LIMIT 1";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }
    

    /**
     * La función `ConsultarMesaMenorImporte()` en la clase `Mesa` está consultando la base de datos
     * para encontrar la tabla `mesas` con el precio total más bajo (`precio_total`) de los pedidos
     * (“pedidos`) dentro de los últimos 30 días.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarMesaMenorImporte()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, pedidos.precio_total AS importe FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY importe ASC LIMIT 1";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }

    /**
     * La función `ConsultarMesasPorOrdenDeFacturacionDescendente()` en la clase `Mesa` está
     * consultando la base de datos para recuperar una lista de tablas (`mesas`) ordenadas por el monto
     * total facturado (`precio_total`) para pedidos (`pedidos`) dentro de los últimos 30 días en orden
     * descendente.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarMesasPorOrdenDeFacturacionDescendente()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, SUM(pedidos.precio_total) AS facturado FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY mesas.id ORDER BY facturado DESC";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }

    /**
     * La función `ConsultarMesasPorOrdenDeFacturacionAscendente()` en la clase `Mesa` está consultando
     * la base de datos para recuperar una lista de tablas (`mesas`) ordenadas por el monto total
     * facturado (`precio_total`) para pedidos (`pedidos`) dentro de los últimos 30 días en orden
     * ascendente según el monto total facturado.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarMesasPorOrdenDeFacturacionAscendente()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT mesas.id, SUM(pedidos.precio_total) AS facturado FROM mesas INNER JOIN pedidos ON mesas.id = pedidos.id_mesa WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY mesas.id ORDER BY facturado ASC";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Mesa');     
    }


    /**
     * 
     * La función `ConsultarFacturacionMesasEntreFechas` en la clase `Mesa` consulta la base de datos
     * para recuperar los ingresos totales ("facturado") para una tabla específica ("mesa") dentro de
     * un rango de fechas determinado.
     * 
     * @param mixed $fechaMin
     * @param mixed $fechaMax
     * @param mixed $idMesa
     * 
     * @return [type]
     * 
     */
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


    /**
     * 
     * La `ModificarMesa(,)` en la clase `Mesa` es un método
     * utilizado para actualizar el campo `estado_mesa` de un registro específico en la tabla `mesas`
     * de la base de datos.
     * 
     * @param mixed $estado
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
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

    /**
     * 
     * La función `BorrarMesa()` en la clase `Mesa` es responsable de
     * eliminar un registro específico de la tabla `mesas` en la base de datos según el `id`
     * proporcionado.
     * 
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
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





