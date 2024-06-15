<?php



function AccederABaseDeDatos($nombreBaseDeDatos)
{
    try
    {
        $pdo = new PDO
        (
            'mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DB'].';charset=utf8', 
            $_ENV['MYSQL_USER'], 
            $_ENV['MYSQL_PASS'], 
            array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        
        $pdo->exec("SET CHARACTER SET utf8");
    }
    catch (PDOException $e)
    {
        echo "Falló la conexión:". $e->getMessage(); 
        exit();
    }

    return $pdo;
}
