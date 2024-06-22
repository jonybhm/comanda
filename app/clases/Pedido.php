<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

class Pedido
{
    private $_id;
    private $_idMesa;
    private $_nombreCliente;
    private $_estadoPedido;
    private $_tiempoPreparacion;
    private $_precioTotal;
    private $_foto;    

    public function __construct($id = NULL, $idMesa = NULL, $nombreCliente = NULL, $estadoPedido = NULL, $tiempoPreparacion = NULL, $precioTotal = NULL, $foto = NULL)
    {
        $this->_id = $id;
        $this->_idMesa = $idMesa;
        $this->_nombreCliente = $nombreCliente;
        $this->_estadoPedido = $estadoPedido;
        $this->_tiempoPreparacion = $tiempoPreparacion;
        $this->_precioTotal = $precioTotal;
        $this->_foto = $foto;        
    }
    
    public function getId()
    {
        return $this->_id;
    }

    static public function AltaPedido($idMesa,$nombreCliente)
    {
        $claveAlfaNumerica = GenerarClaveAlfaNumerica();
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO pedidos (id, id_mesa, nombre_cliente,estado) VALUES (?, ?, ?, 'pendiente')";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1,$claveAlfaNumerica , PDO::PARAM_STR);
            $consulta -> bindValue(2,$idMesa , PDO::PARAM_STR);
            $consulta -> bindValue(3,$nombreCliente , PDO::PARAM_STR);
            // $consulta -> bindValue(4,$estadoPedido , PDO::PARAM_STR);
            // $consulta -> bindValue(5,$precioTotal , PDO::PARAM_STR);
            //$consulta -> bindValue(6,$tiempoPreparacion , PDO::PARAM_INT);
            //$consulta -> bindValue(7,$foto , PDO::PARAM_LOB);
            $consulta -> execute();
            return $claveAlfaNumerica;
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
            return NULL;
        }
    }
    
    static public function ConsultarPedido($id)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM pedidos WHERE id = ?";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $id, PDO::PARAM_STR);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Pedido');
        $elemento = $consulta -> fetch();
        return $elemento;
    }

    static public function ConsultarPedidoPorEstado($estado,$tipo)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT productos_pedidos.id, productos.nombre_producto, productos_pedidos.id_pedido, productos_pedidos.nombre_cliente,productos_pedidos.estado_producto FROM productos_pedidos INNER JOIN productos ON productos_pedidos.id_producto = productos.id WHERE productos_pedidos.estado_producto = ? AND productos.tipo_producto = ?";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $estado, PDO::PARAM_STR);
        $consulta -> bindValue(2, $tipo, PDO::PARAM_STR);
        $consulta -> execute();

        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    static public function ConsultarIdPedidoPorIdProductoPedido($idPedidoProducto)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT productos_pedidos.id_pedido FROM productos_pedidos INNER JOIN productos ON productos_pedidos.id_producto = productos.id WHERE productos_pedidos.id = ? ";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $idPedidoProducto, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Pedido');
        $elemento = $consulta -> fetch();
        return $elemento;
    }
    
    static public function ConsultarTodosLosPedidos()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM pedidos";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Pedido');     
    }

    static public function ModificarPedido($estadoPedido,$tiempoPreparacion,$precioTotal,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE pedidos SET estado = ?, tiempo_preparacion = ?, precio_total = ? WHERE id = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $estadoPedido, PDO::PARAM_STR);
            $consulta -> bindValue(2,$tiempoPreparacion , PDO::PARAM_INT);
            $consulta -> bindValue(3,$precioTotal , PDO::PARAM_STR);
            $consulta -> bindValue(4, $id, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }
    }

    static public function ModificarPrecioPedido($precioTotal,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE pedidos SET precio_total = ? WHERE id = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1,$precioTotal , PDO::PARAM_STR);
            $consulta -> bindValue(2, $id, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }
    }
   
    static public function ModificarTiempoPedido($tiempoFinal,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE pedidos SET tiempo_final = ? WHERE id = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1,$tiempoFinal , PDO::PARAM_INT);
            $consulta -> bindValue(2, $id, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }
    }

    static public function ModificarEstadoPedido($estadoFinal,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE pedidos SET estado = ? WHERE id = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1,$estadoFinal , PDO::PARAM_STR);
            $consulta -> bindValue(2, $id, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }
    }

    static public function BorrarPedido($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM pedidos WHERE id = ?";
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

    static public function ConsultarPedidoMesaMasUsada()
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT *, COUNT(id_mesa) FROM pedidos ORDER BY COUNT(id_mesa) DESC LIMIT 1";


        $consulta = $pdo->prepare($query);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Pedido');
        $elemento = $consulta -> fetch();
        return $elemento;
    }
    
    
} 





