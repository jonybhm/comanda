<?php

/* La clase `TiempoEspera` en PHP representa un modelo para gestionar tiempos de espera con propiedades y métodos para
crear, actualizar y consultar la tabla de pedidos_tiempo en una base de datos. */
class TiempoEspera
{
    private $_id;
    private $_tiempoInicial;
    private $_tiempoFinal;
    private $_entregaEstimada;
    private $_entregaReal;
    private $_entregaTardia;
    private $_idPedido;
    private $_fecha;

    /**
     * [Description for __construct]
     *
     * @param null $id
     * @param null $tiempoInicial
     * @param null $tiempoFinal
     * @param null $entregaEstimada
     * @param null $entregaReal
     * @param null $entregaTardia
     * @param null $idPedido
     * @param null $fecha
     * 
     */
    public function __construct($id = NULL, $tiempoInicial = NULL, $tiempoFinal = NULL, $entregaEstimada = NULL, $entregaReal = NULL, $entregaTardia = NULL, $idPedido = NULL, $fecha = NULL)
    {
        $this->_tiempoInicial = $tiempoInicial;
        $this->_tiempoFinal = $tiempoFinal;
        $this->_id = $id;        
        $this->_entregaEstimada = $entregaEstimada;        
        $this->_entregaReal = $entregaReal;
        $this->_entregaTardia = $entregaTardia;
        $this->_idPedido = $idPedido;
        $this->_fecha = $fecha;
    }
    
    /**
     * La función `AltaTiempoEspera` en la clase `TiempoEspera` es responsable de crear una nueva entrada de pedidos_tiempo
     * en la base de datos. 
     *
     * @param mixed $idPedidoProducto
     * @param mixed $entregaEstimada
     * @param mixed $idPedido
     * 
     * @return [type]
     * 
     */
    static public function AltaTiempoEspera($idPedidoProducto,$entregaEstimada,$idPedido)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO pedidos_tiempo (id_pedido_producto, entrega_estimada, id_pedido) VALUES (?,?,?)";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idPedidoProducto, PDO::PARAM_INT);
            $consulta -> bindValue(2, $entregaEstimada, PDO::PARAM_STR);
            $consulta -> bindValue(3, $idPedido, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }

    }    

    
    /**
     * La funcion ModificarTiempoEsperaFinal() actualiza el tiempo de entrega estimado 
     * y el tiempo de entrega real en base a la diferencia de las timestamps.
     * Estos valores se modifican en la fila que corresponde con el id del pedido_producto
     *
     * @param mixed $tiempoFinal
     * @param mixed $idPedidoProducto
     * 
     * @return [type]
     * 
     */
    static public function ModificarTiempoEsperaFinal($tiempoFinal,$idPedidoProducto)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE pedidos_tiempo SET entregado_timestamp = ?, entrega_real = TIMESTAMPDIFF(MINUTE, preparacion_timestamp, entregado_timestamp) WHERE id_pedido_producto = ?";
        
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $tiempoFinal, PDO::PARAM_STR);
            $consulta -> bindValue(2, $idPedidoProducto, PDO::PARAM_INT);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }

    
    
    /**
     * La funcion ModificarTiempoEsperaAtrasado() actualiza el valor de entrega tardia como verdadero(1) 
     * cuando el tiempo de entrega real se mayor que el de entrega estimada. 
     *
     * @return [type]
     * 
     */
    static public function ModificarTiempoEsperaAtrasado()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE pedidos_tiempo SET entrega_tardia = 1 WHERE entrega_real > entrega_estimada";
        
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }

    /**
     * 
     * El método `ConsultarTiempoEsperaTardios()` en la clase `TiempoEspera` se utiliza para
     * recuperar un tiempo de espera que sido marcado como tardio.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarTiempoEsperaTardios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT id_pedido FROM pedidos_tiempo WHERE entrega_tardia = 1 AND fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY id_pedido DESC";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'TiempoEspera');
    }
}