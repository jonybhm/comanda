<?php

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
    
    //ESTE SE USA CUANDO CAMBIA EL ESTADO A "EN PREPARACION"
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

    //ESTE SE USA CUANDO CAMBIA EL ESTADO A "ENTREGADO"
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

    //ESTE SE USA CUANDO CAMBIA EL ESTADO A "EN PREPARACION" (AL MISMO TIEMPO QUE EL ANTERIOR)
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

    static public function ConsultarTiempoEsperaTardios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT id_pedido FROM pedidos_tiempo WHERE entrega_tardia = 1 AND fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY id_pedido DESC";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'TiempoEspera');
    }
}