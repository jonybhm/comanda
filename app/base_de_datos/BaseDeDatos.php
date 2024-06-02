<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

function AccederABaseDeDatos($nombreBaseDeDatos)
{


    try
    {
        $strConexionUsuarios = 'mysql:host=localhost;dbname='.$nombreBaseDeDatos;
        $pdo = new PDO($strConexionUsuarios,'root','14271824');
    }
    catch (PDOException $e)
    {
        echo "Falló la conexión:". $e->getMessage(); 
        exit();
    }

    return $pdo;
}

function CrearElemento($pdo,$strQuery,$valor,$password,$tipo)
{
    $fecha = new DateTime(date("d-m-Y"));

    try
    {
        $query = $pdo->prepare($strQuery);
        $query -> bindValue(1, $valor, PDO::PARAM_STR);
        $query -> bindValue(2, password_hash($password,PASSWORD_DEFAULT));
        $query -> bindValue(3, $tipo, PDO::PARAM_STR);
        $query -> bindValue(4, date_format($fecha, 'Y-m-d H:i:s'), PDO::PARAM_STR);
        $query -> execute();
    }
    catch(PDOException $e)
    {
        echo "Error al crear elemento: ".$e->getMessage();
    }
}

function ObtenerElemento($pdo,$valorABuscar,$nombreClase,$strQuery)
{
    $query = $pdo->prepare($strQuery);
    $query -> bindValue(1, $valorABuscar, PDO::PARAM_INT);
    $query -> execute();

    $query -> setFetchMode(PDO::FETCH_CLASS,$nombreClase);
    $elemento = $query -> fetch();
    return $elemento;
}

function ObtenerTodosLosElementos($pdo,$nombreTabla,$nombreClase,$strQuery)
{
    $query = $pdo->prepare($strQuery);
    $query -> execute();
    return $query -> fetchAll(PDO::FETCH_CLASS, $nombreClase);
}

function ModificarElemento($pdo,$strQuery,$valor,$password,$tipo,$valorABuscar)
{
    try
    {
        $query = $pdo->prepare($strQuery);
        $query -> bindValue(1, $valor, PDO::PARAM_STR);
        $query -> bindValue(2, password_hash($password,PASSWORD_DEFAULT));
        $query -> bindValue(3, $tipo, PDO::PARAM_STR);
        $query -> bindValue(4, $valorABuscar, PDO::PARAM_INT);
        $query -> execute();
    }
    catch(PDOException $e)
    {
        echo "Error al modificar elemento: ".$e->getMessage();
    }
}

function BorrarElemento($pdo,$strQuery,$valorABuscar)
{
    try
    {
        $query = $pdo->prepare($strQuery);
        $query -> bindValue(1, $valorABuscar, PDO::PARAM_INT);
        $query -> execute();
    }
    catch(PDOException $e)
    {
        echo "Error al elimiar elemento: ".$e->getMessage();
    }
}


