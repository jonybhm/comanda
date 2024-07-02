<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

/* La clase `Pedido` en PHP representa un modelo para gestionar pedidos con propiedades y métodos para
crear, actualizar y consultar pedidos en una base de datos. */
class Pedido
{
    private $_id;
    private $_idMesa;
    private $_nombreCliente;
    private $_estadoPedido;
    private $_tiempoPreparacion;
    private $_precioTotal;
    private $_foto;
    private $_fecha;    
    private $_tiempoInicial;    

    /**
     *
     * @param null $id
     * @param null $idMesa
     * @param null $tiempoInicial
     * @param null $nombreCliente
     * @param null $estadoPedido
     * @param null $tiempoPreparacion
     * @param null $precioTotal
     * @param null $foto
     * @param null $fecha
     * 
     */
    public function __construct($id = NULL, $idMesa = NULL, $tiempoInicial = NULL, $nombreCliente = NULL, $estadoPedido = NULL, $tiempoPreparacion = NULL, $precioTotal = NULL, $foto = NULL,  $fecha = NULL)
    {
        $this->_id = $id;
        $this->_idMesa = $idMesa;
        $this->_nombreCliente = $nombreCliente;
        $this->_estadoPedido = $estadoPedido;
        $this->_tiempoPreparacion = $tiempoPreparacion;
        $this->_precioTotal = $precioTotal;
        $this->_foto = $foto;   
        $this->_fecha = $fecha;        
        $this->_tiempoInicial = $tiempoInicial;        
    }
    
    public function getId()
    {
        return $this->_id;
    }

    /**
     * La función `AltaPedido` en la clase `Pedido` es responsable de crear una nueva entrada de pedido
     * en la base de datos.
     * 
     * @param mixed $idMesa
     * @param mixed $nombreCliente
     * 
     * @return [type]
     * 
     */
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
            $consulta -> execute();
            return $claveAlfaNumerica;
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
            return NULL;
        }
    }
    
    /**
     * 
     * El método `ConsultarPedido()` en la clase `Pedido` se utiliza para
     * recuperar un pedido específico de la base de datos según el ID del pedido proporcionado.
     * 
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
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

    /**
     * El método `ConsultarPedidoPorEstado(,)` en la clase
     * `Pedido` es responsable de consultar la base de datos para recuperar una lista de pedidos
     * basados en los parámetros de estado y tipo proporcionados.
     * 
     * @param mixed $estado
     * @param mixed $tipo
     * 
     * @return [type]
     * 
     */
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

    /**
     * 
     * La función `ConsultarIdPedidoPorIdProductoPedido` en la clase `Pedido` es responsable de
     * consultar la base de datos para recuperar el ID de un pedido específico con base en el ID del
     * pedido del producto proporcionado.
     * 
     * @param mixed $idPedidoProducto
     * 
     * @return [type]
     * 
     */
    static public function ConsultarIdPedidoPorIdProductoPedido($idPedidoProducto)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT productos_pedidos.id_pedido FROM productos_pedidos INNER JOIN productos ON productos_pedidos.id_producto = productos.id WHERE productos_pedidos.id = ? ";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $idPedidoProducto, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Producto');
        $elemento = $consulta -> fetch();
        return $elemento;
    }

    /**
     * La función `ConsultarIdMesaPorIdPedido()` en la clase `Pedido`
     * es responsable de consultar la base de datos para recuperar el ID de la tabla asociada con un ID
     * de pedido específico.
     * 
     * @param mixed $idPedido
     * 
     * @return [type]
     * 
     */
    static public function ConsultarIdMesaPorIdPedido($idPedido)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT pedidos.id_mesa FROM pedidos INNER JOIN productos_pedidos ON productos_pedidos.id_pedido = pedidos.id WHERE pedidos.id = ? ";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $idPedido, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Pedido');
        $elemento = $consulta -> fetch();
        return $elemento;
    }

    /**
     * 
     * La función `ConsultarTodosLosPedidos()` en la clase `Pedido` es responsable de consultar la base
     * de datos para recuperar todos los pedidos almacenados en la tabla `pedidos`.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarTodosLosPedidos()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT id, id_mesa, nombre_cliente, estado, precio_total, tiempo_final AS tiempo_estimado FROM pedidos";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Pedido');     
    }

    /**
     * 
     * Esta función consulta una tabla de base de datos llamada "pedidos" para recuperar los ID de todos los
     * pedidos que se cancelaron en los últimos 30 días. La consulta selecciona la columna "id" de la
     * tabla "pedidos" donde la columna "estado" es igual a "cancelado" y la columna "fecha" está
     * dentro de los últimos 30 días a partir de la fecha actual.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarCancelados()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT id FROM pedidos WHERE estado = 'cancelado' AND fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY)";

        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Pedido');     
    }


    /**
     * 
     * La `ModificarPedido(,)` en la clase `Pedido` es un método
     * utilizado para actualizar un registro específico en la tabla `pedidos`
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

    /**
     * ModificarPrecioPedido se encarga de actualizar el precio 
     * de un pedido específico en una tabla de base de datos "pedidos".
     *
     * @param mixed $precioTotal
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
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
   

    /**
     * 
     * ModificarTiempoPedido se encarga de actualizar el campo 
     * tiempo_final (hora final) en la tabla pedidos de una base de datos.
     * 
     * @param mixed $tiempoFinal
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
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


    /**
     * ModificarEstadoPedido es responsable de actualizar el estado 
     * de un pedido en una tabla de base de datos pedidos.
     * 
     * @param mixed $estadoFinal
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
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

    /**
     * `BorrarPedido` se encarga de eliminar un registro 
     * de la tabla `pedidos` en una base de datos. 
     * 
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
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

    /**
     * 
     * `ConsultarPedidoMesaMasUsada` dentro de una clase. 
     * Este método consiste en consultar una tabla de base de datos llamada "pedidos" para
     * encontrar la tabla más utilizada en los últimos 30 días en función del recuento de pedidos
     * realizados en cada tabla.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarPedidoMesaMasUsada()
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT *, COUNT(id_mesa) FROM pedidos WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY id_mesa ORDER BY COUNT(id_mesa) DESC LIMIT 1";


        $consulta = $pdo->prepare($query);
        $consulta -> execute();

        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Pedido');  
    }

    /**
     * 
     * Este método se utiliza para consultar una tabla de base de datos llamada
     * "pedidos" para encontrar la tabla menos utilizada en los últimos 30 días según la cantidad de
     * pedidos realizados en cada tabla.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarPedidoMesaMenosUsada()
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT *, COUNT(id_mesa) FROM pedidos WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY id_mesa ORDER BY COUNT(id_mesa) ASC LIMIT 1";


        $consulta = $pdo->prepare($query);
        $consulta -> execute();

        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Pedido');  
    }

    /**
     * Este método se utiliza para consultar una tabla de base de datos llamada
     * "pedidos" para encontrar los pedidos con estado de entregados.
     * 
     * @param mixed $idPedido
     * 
     * @return [type]
     * 
     */
    
    static public function ConsultarPedidoEntregados($idPedido)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM pedidos WHERE estado = 'entregado' AND id_pedido=?'";


        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $idPedido, PDO::PARAM_STR);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Pedido');
        $elemento = $consulta -> fetch();
        return $elemento;
    }
} 
