<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

class PedidoProducto
{
    private $_id;
    private $_nombreProducto;
    private $_precioProducto;
    private $_tipoProducto;

    public function __construct($id = NULL, $nombreProducto = NULL, $precioProducto = NULL, $tipoProducto = NULL)
    {
        $this->_nombreProducto = $nombreProducto;
        $this->_precioProducto = $precioProducto;
        $this->_id = $id;        
        $this->_tipoProducto = $tipoProducto;        

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
    
    

    static public function ModificarProductoPedido($estado,$tiempo,$idPedidoProducto,$tipoProducto)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE productos_pedidos INNER JOIN productos ON productos_pedidos.id_producto = productos.id SET estado_producto = ?, tiempo_estimado = ? WHERE productos_pedidos.id = ? AND productos.tipo_producto = ?";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $estado, PDO::PARAM_STR);
            $consulta -> bindValue(2, $tiempo, PDO::PARAM_STR);
            $consulta -> bindValue(3, $idPedidoProducto, PDO::PARAM_STR);
            //echo $tipoProducto;
            $consulta -> bindValue(4, $tipoProducto, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }

    static public function EliminarPedidoLuegoDeCobrar($idMesa)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM servicio WHERE id_mesa = ?";
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




