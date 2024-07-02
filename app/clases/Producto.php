<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

/* La clase `Produto` en PHP representa un modelo para gestionar Produtos con propiedades y métodos para
crear, actualizar y consultar Produtos en una base de datos. */
class Producto
{
    private $_id;
    private $_nombreProducto;
    private $_precioProducto;
    private $_tipoProducto;

    /**
     * [Description for __construct]
     *
     * @param null $id
     * @param null $nombreProducto
     * @param null $precioProducto
     * @param null $tipoProducto
     * 
     */
    public function __construct($id = NULL, $nombreProducto = NULL, $precioProducto = NULL, $tipoProducto = NULL)
    {
        $this->_nombreProducto = $nombreProducto;
        $this->_precioProducto = $precioProducto;
        $this->_id = $id;        
        $this->_tipoProducto = $tipoProducto;
    }
    
  


    /**
     * La función `AltaProducto` en la clase `Producto` es responsable de crear una nueva entrada de Producto
     * en la base de datos.
     * 
     * @param mixed $nombre
     * @param mixed $precio
     * @param mixed $tipo
     * 
     * @return [type]
     * 
     */
    static public function AltaProducto($nombre,$precio,$tipo)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO productos (nombre_producto, precio_producto, tipo_producto) VALUES (?,?,?)";
       
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombre, PDO::PARAM_STR);
            $consulta -> bindValue(2, $precio, PDO::PARAM_STR);
            $consulta -> bindValue(3, $tipo, PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }
    }
    
    /**
     * 
     * El método `ConsultarProducto()` en la clase `Producto` se utiliza para
     * recuperar un Producto específico de la base de datos según el ID del Producto proporcionado.
     * 
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
    static public function ConsultarProducto($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM productos WHERE id = ?";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $id, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Producto');
        $elemento = $consulta -> fetch();
        return $elemento;
    }
    
    /**
     * 
     * La función `ConsultarTodosLosProductos()` en la clase `Pedido` es responsable de consultar la base
     * de datos para recuperar todos los Productos almacenados en la tabla `Productos`.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarTodosLosProductos()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM productos";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Producto');
    }


    /**
     * La funcion ConsultarProductosDelMasVendidoAlMenos consulta la tabla de SQL para obtener 
     * los productos ordenados del mas vendido al menos vendido 
     *
     * @return [type]
     * 
     */
    static public function ConsultarProductosDelMasVendidoAlMenos()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT  productos.nombre_producto, COUNT(productos_pedidos.id_producto) AS cantidad FROM productos_pedidos INNER JOIN productos ON productos_pedidos.id_producto=productos.id WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY productos.nombre_producto ORDER BY cantidad DESC";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    /**
     * La funcion ConsultarProductosDelMasVendidoAlMenos consulta la tabla de SQL para obtener 
     * los productos ordenados del menos vendido al mas vendido 
     *
     * @return [type]
     * 
     */
    static public function ConsultarProductosDelMenosVendidoAlMas()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT  productos.nombre_producto, COUNT(productos_pedidos.id_producto) AS cantidad FROM productos_pedidos INNER JOIN productos ON productos_pedidos.id_producto=productos.id WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY productos.nombre_producto ORDER BY cantidad ASC";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    
    /**
     * 
     * La `ModificarProducto(,)` en la clase `Producto` es un método
     * utilizado para actualizar un registro específico en la tabla `productos`
     * de la base de datos.
     *
     * @param mixed $estadoPedido
     * @param mixed $tiempoPreparacion
     * @param mixed $precioTotal
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
    
    static public function ModificarProducto($nombre,$precio,$tipo,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE productos SET nombre_producto = ?, precio_producto = ?, tipo_producto = ? WHERE id = ?";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombre, PDO::PARAM_STR);
            $consulta -> bindValue(2, $precio, PDO::PARAM_STR);
            $consulta -> bindValue(3, $tipo, PDO::PARAM_STR);
            $consulta -> bindValue(4, $id, PDO::PARAM_INT);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }

    /**
     * `BorrarProducto` se encarga de eliminar un registro 
     * de la tabla `Productos` en una base de datos. 
     * 
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
    static public function BorrarProducto($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM productos WHERE id = ?";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $id, PDO::PARAM_INT);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al elimiar elemento: ".$e->getMessage();
        }
    }
} 





