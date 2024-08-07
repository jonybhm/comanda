<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

/* La clase `Encuesta` en PHP representa una entidad de encuesta con propiedades relacionadas con
calificaciones, comentarios y fechas, junto con métodos para insertar datos de encuesta, consultar
información de tiempo para un pedido específico y recuperar las cinco encuestas superiores e
inferiores según las calificaciones de la tabla dentro. los últimos 30 días. */
class Encuesta
{
    private $_id;
    private $_puntajeMesa;
    private $_puntajeRestaurante;
    private $_idMesa;
    private $_idPedido;
    private $_puntajeMozo;
    private $_puntajeCocinero;
    private $_comentario;  
    private $_fecha;    

    /**
     *
     * @param null $id
     * @param null $puntajeMesa
     * @param null $fecha
     * @param null $puntajeRestaurante
     * @param null $idMesa
     * @param null $idPedido
     * @param null $puntajeMozo
     * @param null $comentario
     * @param null $puntajeCocinero
     * 
     */
    public function __construct($id = NULL, $puntajeMesa = NULL, $fecha = NULL, $puntajeRestaurante = NULL, $idMesa = NULL, $idPedido = NULL, $puntajeMozo = NULL, $comentario = NULL, $puntajeCocinero = NULL)
    {
        $this->_id = $id;
        $this->_puntajeMesa = $puntajeMesa;
        $this->_puntajeRestaurante = $puntajeRestaurante;
        $this->_idMesa = $idMesa;
        $this->_idPedido = $idPedido;
        $this->_puntajeMozo = $puntajeMozo;
        $this->_puntajeCocinero = $puntajeCocinero;
        $this->_comentario = $comentario;        
        $this->_fecha = $fecha;        
    }
    
    /**
     * La `AltaEncuesta` en la clase `Encuesta` es un método estático
     * utilizado para insertar un nuevo registro de encuesta en la base de datos. 
     *
     * @param mixed $puntajeMesa
     * @param mixed $puntajeRestaurante
     * @param mixed $idMesa
     * @param mixed $idPedido
     * @param mixed $puntajeMozo
     * @param mixed $comentario
     * @param mixed $puntajeCocinero
     * 
     * @return [type]
     * 
     */
    
    static public function AltaEncuesta($puntajeMesa,$puntajeRestaurante,$idMesa,$idPedido,$puntajeMozo,$comentario,$puntajeCocinero)
    {
        $claveAlfaNumerica = GenerarClaveAlfaNumerica();
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO encuesta (puntaje_mesa, puntaje_restaurante, id_mesa, id_pedido, puntaje_mozo, puntaje_cocinero, comentario) VALUES (?, ?, ?, ?, ?, ?, ?)";
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1,$puntajeMesa , PDO::PARAM_STR);
            $consulta -> bindValue(2,$puntajeRestaurante , PDO::PARAM_STR);
            $consulta -> bindValue(3,$idMesa , PDO::PARAM_STR);
            $consulta -> bindValue(4,$idPedido , PDO::PARAM_STR);
            $consulta -> bindValue(5,$puntajeMozo , PDO::PARAM_STR);
            $consulta -> bindValue(6,$puntajeCocinero , PDO::PARAM_STR);
            $consulta -> bindValue(7,$comentario , PDO::PARAM_INT);
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
     * El método `ConsultarTiempoPedido(,)` en la clase
     * `Encuesta` se utiliza para consultar la base de datos en busca de información relacionada con el
     * tiempo de una orden específica. 
     *
     * @param mixed $idMesa
     * @param mixed $idPedido
     * 
     * @return [type]
     * 
     */
    static public function ConsultarTiempoPedido($idMesa,$idPedido)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT tiempo_inicial AS registrado ,tiempo_final AS estimado,  estado FROM pedidos WHERE id = ? AND id_mesa = ?";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $idPedido, PDO::PARAM_STR);
        $consulta -> bindValue(2, $idMesa, PDO::PARAM_STR);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Pedido');
        $elemento = $consulta -> fetch();
        return $elemento;
    }


    /**
     * 
     * El método `ConsultarTopCincoEncuestas()` en la clase `Encuesta` se
     * utiliza para recuperar las cinco encuestas principales según las calificaciones de la tabla
     * dentro de los últimos 30 días de la base de datos. 
     * 
     * @return [type]
     * 
     */

    static public function ConsultarTopCincoEncuestas()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM encuesta WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY puntaje_mesa DESC LIMIT 5";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();

        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Encuesta');
        
    }

    /**
     * 
     * El método `ConsultarBottomCincoEncuestas()` en la clase `Encuesta` se
     * utiliza para recuperar las cinco últimas encuestas basadas en las calificaciones de la base de
     * datos dentro de los últimos 30 días. 
     * 
     * @return [type]
     * 
     */
    static public function ConsultarBottomCincoEncuestas()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM encuesta WHERE fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY puntaje_mesa ASC LIMIT 5";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();

        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Encuesta');
        
    }
} 





