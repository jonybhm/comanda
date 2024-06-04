<?php

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
