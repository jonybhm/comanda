
<?php

/* La clase `LogUsuario` en PHP está diseñada para manejar el registro de acciones del usuario con
métodos para registrar registros, recuperar registros por ID y recuperar todos los registros. */
class LogUsuario
{
    private $_id;
    private $_idUsuario;
    private $_accionTomada;
    private $_fecha;
    private $_hora;

    /**
     *
     * @param null $id
     * @param null $idUsuario
     * @param null $accionTomada
     * @param null $fecha
     * @param null $hora
     * 
     */
    public function __construct($id = NULL, $idUsuario = NULL, $accionTomada = NULL, $fecha = NULL, $hora = NULL)
    {
        $this->_idUsuario = $idUsuario;
        $this->_accionTomada = $accionTomada;
        $this->_id = $id;        
        $this->_fecha = $fecha;        
        $this->_hora = $hora;        

    }
    
    /**
     * 
     * La función `RegistrarLog` inserta una entrada de registro para una acción del usuario en una
     * tabla de base de datos en PHP.     
     * 
     * @param mixed $idUsuario
     * @param mixed $accionTomada
     * 
     * @return [type]
     * 
     */
    
    public static function RegistrarLog($idUsuario,$accionTomada)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO log_usuarios (id_usuario, accion_tomada) VALUES (?,?)";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $idUsuario, PDO::PARAM_STR);
            $consulta -> bindValue(2, $accionTomada, PDO::PARAM_STR);
            $consulta -> execute();

            $lastInsertId = $pdo->lastInsertId();
            return $lastInsertId;
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }
    }

    /**
     * 
     * El método `ObtenerLog()` en la clase `LogUsuario` se utiliza para
     * recuperar una entrada de registro de la base de datos basada en el `id` proporcionado.
     * 
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
    static public function ObtenerLog($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM log_usuarios WHERE id = ?";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $id, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'LogUsuario');
        $elemento = $consulta -> fetch();
        return $elemento;
    }
    
    /**
     * 
     * El método `ObtenerTodosLosLogs()` en la clase `LogUsuario` recupera todas las entradas del
     * registro de la tabla de la base de datos `log_usuarios`. 
     *  
     * @return [type]
     * 
     */
    static public function ObtenerTodosLosLogs()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM log_usuarios";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'LogUsuario');
    }
}