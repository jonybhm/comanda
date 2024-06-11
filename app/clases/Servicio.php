<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

class Servicio
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
    
    static public function AltaProductosPedido($idPedido,$idMesa,$nombreCliente,$nombreProducto,$cantidadProducto)
    {
        $pdo = AccederABaseDeDatos('comanda');      

        #==============================PRODUCTOS======================================

        $query = "SELECT * FROM productos WHERE nombre_producto = ? LIMIT 1";
       
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombreProducto, PDO::PARAM_STR);
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
        $query = "INSERT INTO servicio (id_mesa, nombre_cliente, nombre_producto, cantidad_producto, id_pedido, id_producto, precio_producto, tipo_producto,estado_producto) VALUES (?,?,?,?,?,?,?,?,'pendiente')";
       
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idMesa, PDO::PARAM_STR);
            $consulta -> bindValue(2, $nombreCliente, PDO::PARAM_STR);
            $consulta -> bindValue(3, $nombreProducto, PDO::PARAM_STR);
            $consulta -> bindValue(4, $cantidadProducto, PDO::PARAM_INT);
            $consulta -> bindValue(5, $idPedido, PDO::PARAM_STR);
            $consulta -> bindValue(6, $productoId, PDO::PARAM_INT);
            $consulta -> bindValue(7, $productoPrecio, PDO::PARAM_STR);
            $consulta -> bindValue(8, $productoTipo, PDO::PARAM_STR);

            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento en servicio: ".$e->getMessage();
        }

        return $precioProductoXCantidad = $productoPrecio * $cantidadProducto;
    }
    
    

    static public function ModificarProductoPedido($estado,$tiempo,$idMesa,$nombreProducto)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE servicio SET estado_producto = ?, tiempo_estimado = ? WHERE id_mesa = ? AND nombre_producto = ?";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $estado, PDO::PARAM_STR);
            $consulta -> bindValue(2, $tiempo, PDO::PARAM_STR);
            $consulta -> bindValue(3, $idMesa, PDO::PARAM_STR);
            //echo $nombreProducto;
            $consulta -> bindValue(4, $nombreProducto, PDO::PARAM_STR);
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





