<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

/**
 * La clase `PedidoProducto` en PHP contiene métodos para gestionar pedidos de productos, como agregar
 * productos a un pedido específico, modificar el estado del producto y el tiempo estimado, cancelar
 * pedidos, agregar fotos y recuperar información del pedido. 
 * */

class PedidoProducto
{
    private $_id;
    private $_idPedido;
    private $_idProducto;
    private $_nombreCliente;
    private $_estadoProducto;
    private $_tiempoEstimado;
    private $_fecha;
    

    /**
     *
     * @param null $id
     * @param null $idPedido
     * @param null $idProducto
     * @param null $nombreCliente
     * @param null $pestadoroducto
     * @param null $tiempoEstimado
     * @param null $fecha
     * 
     */
    public function __construct($id = NULL, $idPedido = NULL, $idProducto = NULL, $nombreCliente = NULL, $pestadoroducto = NULL, $tiempoEstimado = NULL, $fecha = NULL)
    {
        $this->_id = $id;
        $this->_idPedido = $idPedido;
        $this->_idProducto = $idProducto;
        $this->_nombreCliente = $nombreCliente;
        $this->_estadoProducto = $pestadoroducto;
        $this->_tiempoEstimado = $tiempoEstimado;        
        $this->_fecha = $fecha;        

    }
    
    /**
     * El método `     * AltaProductosPedido(,,,)` en la clase
     * `PedidoProducto` es responsable de agregar productos a un pedido específico. 
     * 
     * @param mixed $idPedido
     * @param mixed $nombreCliente
     * @param mixed $idProducto
     * @param mixed $cantidadProducto
     * 
     * @return [type]
     * 
     */

    static public function AltaProductosPedido($idPedido,$nombreCliente,$idProducto,$cantidadProducto)
    {
        $pdo = AccederABaseDeDatos('comanda');      

        #---------------------PRODUCTOS---------------------

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

        

        #--------------------------SERVICIO--------------------------

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

    /**
     * El método `ModificarProductoPedido(,,)`
     * de la clase `PedidoProducto` se encarga de actualizar el estado y tiempo estimado de un producto
     * en un pedido específico. 
     *  
     * @param mixed $estado
     * @param mixed $tiempo
     * @param mixed $idPedidoProducto
     * 
     * @return [type]
     * 
     */
    
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
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }
        #==============================CANCELAR PEDIDOS======================================

    /**
     * La CancelarProductoPedido(,)` en la clase
     * `PedidoProducto` es responsable de actualizar el campo `estado_producto` en la tabla
     * `productos_pedidos` al valor `` proporcionado para un pedido específico identificado por `idPedido`.
     * 
     * @param mixed $estado
     * @param mixed $idPedido
     * 
     * @return [type]
     * 
     */
    
    static public function CancelarProductoPedido($estado,$idPedido)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE productos_pedidos SET estado_producto = ? WHERE id_pedido = ?";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $estado, PDO::PARAM_STR);
            $consulta -> bindValue(2, $idPedido, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }
    
    #==============================AGREGAR FOTO======================================

    
 
    /**
     * El método `AgregarFoto(,)` es responsable de actualizar
     * el campo `foto` en la tabla `pedidos` con la foto proporcionada para un `id` específico. Utiliza
     * PDO para preparar y ejecutar una consulta SQL que actualiza el campo `foto` en la tabla
     * `pedidos` según los valores proporcionados `` y ``. Si se produce un error durante
     * la ejecución de la consulta, detecta la PDOException y emite un mensaje de error que indica el
     * problema.
     *
     * @param mixed $foto
     * @param mixed $idPedido
     * 
     * @return [type]
     * 
     */
    
    static public function AgregarFoto($foto,$idPedido)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE pedidos SET foto = ? WHERE id = ?";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $foto, PDO::PARAM_STR);
            $consulta -> bindValue(2, $idPedido, PDO::PARAM_STR);
            
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al subir foto: ".$e->getMessage();
        }

    }

    #==============================TRAER TODOS LOS ESTADOS======================================


    /**
     * * 
     * La `TraerTodosLosPedidosProductos()` en la clase
     * `PedidoProducto` es responsable de recuperar todos los productos relacionados con un pedido
     * específico identificado por el `idPedido`.
     * 
     * @param mixed $idPedido
     * 
     * @return [type]
     * 
     */
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

    #==============================TRAER TODOS LOS ESTADOS======================================


    /**
     * 
     * El método `ConsultarTipoProducto()` de la clase
     * `PedidoProducto` se encarga de consultar la base de datos para recuperar el tipo de producto
     * asociado a un pedido específico identificado por el `idPedido`. 
     * 
     * @param mixed $idPedido
     * 
     * @return [type]
     * 
     */
    static public function ConsultarTipoProducto($idPedido)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT productos.tipo_producto FROM productos INNER JOIN productos_pedidos ON productos_pedidos.id_producto = productos.id WHERE productos_pedidos.id = ?";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idPedido, PDO::PARAM_STR);
            $consulta -> execute();

            $consulta -> setFetchMode(PDO::FETCH_CLASS,'PedidoProducto');
            $elemento = $consulta -> fetch();
            return $elemento;        
        }
        catch(PDOException $e)
        {
            echo "Error al elimiar elemento: ".$e->getMessage();
        }
    } 

    /**
     * El método `EliminarPedidoLuegoDeCobrar()` en la clase
     * `PedidoProducto` es responsable de eliminar todas las entradas de productos relacionadas con un
     * pedido específico identificado por `idMesa` después de que el pedido haya sido pagado.
     *
     * @param mixed $idMesa
     * 
     * @return [type]
     * 
     */
    
    static public function EliminarPedidoLuegoDeCobrar($idMesa)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM productos_pedidos WHERE id_pedido = ?";
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





