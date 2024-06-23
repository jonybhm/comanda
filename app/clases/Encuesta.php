<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";

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
    
    static public function ConsultarTiempoPedido($idMesa,$idPedido)
    {

        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT tiempo_final,tiempo_inicial FROM pedidos WHERE id = ? AND id_mesa = ?";

        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $idPedido, PDO::PARAM_STR);
        $consulta -> bindValue(2, $idMesa, PDO::PARAM_STR);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Pedido');
        $elemento = $consulta -> fetch();
        return $elemento;
    }

    static public function ConsultarTopCincoEncuestas()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM encuesta ORDER BY puntaje_mesa DESC LIMIT 5";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();

        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Encuesta');
        
    }

    static public function ConsultarBottomCincoEncuestas()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM encuesta ORDER BY puntaje_mesa ASC LIMIT 5";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();

        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Encuesta');
        
    }
} 





