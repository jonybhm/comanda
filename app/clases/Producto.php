<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

class Producto
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
    
    static public function ConsultarTodosLosProductos()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM productos";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

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





