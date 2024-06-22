<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

class PedidoProducto
{
    private $_id;
    private $_idPedido;
    private $_idProducto;
    private $_nombreCliente;
    private $_estadoProducto;
    private $_tiempoEstimado;

    public function __construct($id = NULL, $idPedido = NULL, $idProducto = NULL, $nombreCliente = NULL, $pestadoroducto = NULL, $tiempoEstimado = NULL)
    {
        $this->_id = $id;
        $this->_idPedido = $idPedido;
        $this->_idProducto = $idProducto;
        $this->_nombreCliente = $nombreCliente;
        $this->_estadoProducto = $pestadoroducto;
        $this->_tiempoEstimado = $tiempoEstimado;        

    }
    
    static public function AltaProductosPedido($idPedido,$nombreCliente,$idProducto,$cantidadProducto)
    {
        $pdo = AccederABaseDeDatos('comanda');      

        #==============================PRODUCTOS======================================

        $query = "SELECT * FROM productos WHERE id = ? LIMIT 1";
       
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idProducto, PDO::PARAM_STR);
            $consulta -> execute();
            $consulta -> setFetchMode(PDO::FETCH_CLASS,'Producto');
            $producto = $consulta -> fetch();

            foreach($producto as $key => $value)
            {
                switch($key)
                {
                    case "id":
                        $productoId = $value;
                        break;
                    case "nombre_producto":
                        $productoNombre = $value;
                        break;
                    case "precio_producto":
                        $productoPrecio = $value;
                        break;
                    case "tipo_producto":
                        $productoTipo = $value;
                        break;                    
                }
            }            

        }
        catch(PDOException $e)
        {
            echo "Error al obtener producto: ".$e->getMessage();
        }       

        

        #==============================SERVICIO======================================
        $query = "INSERT INTO productos_pedidos (id_pedido, id_producto, nombre_cliente, estado_producto, tiempo_estimado) VALUES (?, ?, ?, 'pendiente', NULL)";
       
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idPedido, PDO::PARAM_STR);
            $consulta -> bindValue(2, $productoId, PDO::PARAM_INT);
            $consulta -> bindValue(3, $nombreCliente, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento en servicio: ".$e->getMessage();
        }

        return $precioProductoXCantidad = $productoPrecio * $cantidadProducto;
    }
    
    
        #==============================MODIFICAR ESTADO======================================

    static public function ModificarProductoPedido($estado,$tiempo,$idPedidoProducto)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE productos_pedidos INNER JOIN productos ON productos_pedidos.id_producto = productos.id SET estado_producto = ?, tiempo_estimado = ? WHERE productos_pedidos.id = ?";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $estado, PDO::PARAM_STR);
            $consulta -> bindValue(2, $tiempo, PDO::PARAM_STR);
            $consulta -> bindValue(3, $idPedidoProducto, PDO::PARAM_STR);
            //echo $tipoProducto;
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }
    
    #==============================AGREGAR FOTO======================================

    static public function AgregarFoto($foto,$idMesa)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE pedidos SET foto = ? WHERE id_mesa = ?";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $foto, PDO::PARAM_LOB);
            $consulta -> bindValue(2, $idMesa, PDO::PARAM_STR);
            
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al subir foto: ".$e->getMessage();
        }

    }

    #==============================TRAER TODOS LOS ESTADOS======================================


    static public function TraerTodosLosPedidosProductos($idPedido)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM productos_pedidos WHERE id_pedido = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idPedido, PDO::PARAM_STR);
            $consulta -> execute();
            return $consulta -> fetchAll(PDO::FETCH_CLASS, 'PedidoProducto'); 
        }
        catch(PDOException $e)
        {
            echo "Error al elimiar elemento: ".$e->getMessage();
        }
    } 

    static public function EliminarPedidoLuegoDeCobrar($idMesa)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM productos_pedidos WHERE id_mesa = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idMesa, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al elimiar elemento: ".$e->getMessage();
        }
    }

    
    
    
} 





