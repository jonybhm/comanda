<?php

include_once "./auxiliar/Auxiliar.php";
include_once "./base_de_datos/BaseDeDatos.php";
date_default_timezone_set('America/Argentina/Buenos_Aires');

/* La clase `Usuario` en PHP representa un modelo para gestionar Usuarios con propiedades y métodos para
crear, actualizar y consultar Usuarios en una base de datos. */
class Usuario
{
    private $_id;
    private $_nombreUsuario;
    private $_password;
    private $_tipoEmpleado;
    private $_fechaIngreso;
    private $_estado;

    /**
     *
     * @param null $id
     * @param null $nombreUsuario
     * @param null $estado
     * @param null $password
     * @param null $tipoEmpleado
     * @param null $fechaIngreso
     * 
     */
    public function __construct($id = NULL, $nombreUsuario = NULL, $estado = NULL, $password = NULL, $tipoEmpleado = NULL, $fechaIngreso = NULL)
    {
        $this->_nombreUsuario = $nombreUsuario;
        $this->_password = $password;
        $this->_id = $id;        
        $this->_tipoEmpleado = $tipoEmpleado;        
        $this->_fechaIngreso = $fechaIngreso;        
        $this->_estado = $estado;        
    }

    /**
     * La función `AltaUsuario` en la clase `Usuario` es responsable de crear una nueva entrada de Usuario
     * en la base de datos.
     *
     * @param mixed $nombreUsuario
     * @param mixed $password
     * @param mixed $tipo
     * 
     * @return [type]
     * 
     */
    static public function AltaUsuario($nombreUsuario,$password,$tipo)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "INSERT INTO usuarios (nombre_usuario, contraseña, tipo_empleado, estado) VALUES (?,?,?,?)";

        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_STR);
            $consulta -> bindValue(2, password_hash($password,PASSWORD_DEFAULT));
            $consulta -> bindValue(3, $tipo, PDO::PARAM_STR);
            $consulta -> bindValue(4, "Activo", PDO::PARAM_STR);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }

    }
    
    /**
     * El método `ConsultarUsuario()` en la clase `Usuario` se utiliza para
     * recuperar un Usuario específico de la base de datos según el ID del Usuario proporcionado.
     * 
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
    static public function ConsultarUsuario($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios WHERE id = ?";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $id, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Usuario');
        $elemento = $consulta -> fetch();
        return $elemento;
    }

    /**
     * El método `ConsultarUsuarioPorNombre()` en la clase `Usuario` se utiliza para
     * recuperar un Usuario específico de la base de datos según el nombre del Usuario proporcionado.
     * 
     * @param mixed $nombreUsuario
     * 
     * @return [type]
     * 
     */
    static public function ConsultarUsuarioPorNombre($nombreUsuario)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_INT);
        $consulta -> execute();

        $consulta -> setFetchMode(PDO::FETCH_CLASS,'Usuario');
        $elemento = $consulta -> fetch();
        return $elemento;
    }
    
    
    /**
     * La función `ConsultarTodosLosUsuarios()` en la clase `Usuario` es responsable de consultar la base
     * de datos para recuperar todos los Usuarios almacenados en la tabla `Usuarios`.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarTodosLosUsuarios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    /**
     * La función `ConsultarTodosLosEstadosDeUsuarios()` en la clase `Usuario` es responsable de consultar la base
     * de datos para recuperar todos los estados de Usuarios almacenados en la tabla `Usuarios`.
     * 
     * @return [type]
     * 
     */
    static public function ConsultarTodosLosEstadosDeUsuarios()
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT nombre_usuario,estado FROM usuarios";
        
        $consulta = $pdo->prepare($query);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    /**
     * La función `ConsultarCantidadOPeracionesUsuarioSector()` en la clase `Usuario` es responsable de consultar la base
     * de datos para recuperar la cantidad de operaciones realizadas por cada usuario dado un tipo de Usuario especifico
     * 
     * @param mixed $tipoEmpleado
     * 
     * @return [type]
     * 
     */
    static public function ConsultarCantidadOPeracionesUsuarioSector($tipoEmpleado)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT usuarios.tipo_empleado, COUNT(log_usuarios.id_usuario) AS cantidad_operaciones FROM log_usuarios INNER JOIN usuarios ON log_usuarios.id_usuario = usuarios.id WHERE usuarios.tipo_empleado = ? AND fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY usuarios.tipo_empleado DESC ";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $tipoEmpleado, PDO::PARAM_INT);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    /**
     * La función `ConsultarCantidadOPeracionesUsuarioNombre()` en la clase `Usuario` es responsable de consultar la base
     * de datos para recuperar la cantidad de operaciones realizadas por cada usuario dado un nombre de Usuario especifico
     * 
     * @param mixed $tipoEmpleado
     * @param mixed $nombreUsuario
     * 
     * @return [type]
     * 
     */
    static public function ConsultarCantidadOPeracionesUsuarioNombre($tipoEmpleado,$nombreUsuario)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT usuarios.nombre_usuario, COUNT(log_usuarios.id_usuario) AS cantidad_operaciones FROM log_usuarios INNER JOIN usuarios ON log_usuarios.id_usuario = usuarios.id WHERE usuarios.tipo_empleado = ? AND usuarios.nombre_usuario=? AND fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY usuarios.tipo_empleado ORDER BY cantidad_operaciones DESC ";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $tipoEmpleado, PDO::PARAM_INT);
        $consulta -> bindValue(2, $nombreUsuario, PDO::PARAM_STR);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    /**
     * La función `ConsultarCantidadOPeracionesUsuarioSectoryNombre()` en la clase `Usuario` es responsable de consultar la base
     * de datos para recuperar la cantidad de operaciones realizadas por cada usuario dado un nombre de Usuario y sector especifico
     * 
     * @param mixed $tipoEmpleado
     * 
     * @return [type]
     * 
     */
    static public function ConsultarCantidadOPeracionesUsuarioSectoryNombre($tipoEmpleado)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT usuarios.nombre_usuario, COUNT(log_usuarios.id_usuario) AS cantidad_operaciones FROM log_usuarios INNER JOIN usuarios ON log_usuarios.id_usuario = usuarios.id WHERE usuarios.tipo_empleado = ? AND fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY usuarios.nombre_usuario ORDER BY cantidad_operaciones DESC ";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $tipoEmpleado, PDO::PARAM_INT);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    /**
     * La función `ConsultarLogeoUsuario()` en la clase `Usuario` es responsable de consultar la base
     * de datos para recuperar la cantidad de logins realizados por cada usuario dado un nombre de Usuario especifico
     * 
     * @param mixed $nombreUsuario
     * 
     * @return [type]
     * 
     */
    static public function ConsultarLogeoUsuario($nombreUsuario)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT usuarios.nombre_usuario, log_usuarios.accion_tomada, log_usuarios.fecha,log_usuarios.hora FROM log_usuarios INNER JOIN usuarios ON log_usuarios.id_usuario = usuarios.id WHERE usuarios.nombre_usuario=? AND accion_tomada LIKE '%sesion%' AND fecha >=  DATE_SUB(NOW(), INTERVAL 30 DAY) ";
        
        $consulta = $pdo->prepare($query);
        $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_INT);
        $consulta -> execute();
        return $consulta -> fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    /**
     * 
     * La `ModificarUsuario(,)` en la clase `Usuario` es un método
     * utilizado para actualizar un registro específico en la tabla `Usuarios`
     * de la base de datos.
     * 
     * @param mixed $nombreUsuario
     * @param mixed $password
     * @param mixed $tipo
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
    static public function ModificarUsuario($nombreUsuario,$password,$tipo,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE usuarios SET nombre_usuario = ?, contraseña = ?, tipo_empleado = ? WHERE id = ?";
        
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_STR);
            $consulta -> bindValue(2, password_hash($password,PASSWORD_DEFAULT));
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
     * `BorradoOSuspencionUsuario` se encarga de actualizar un registro 
     * de la tabla `usuarios` en una base de datos, con estados de 'suspendido' o 'borrado'. 
     * 
     * @param mixed $nuevoEstado
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
    static public function BorradoOSuspencionUsuario($nuevoEstado,$id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "UPDATE usuarios SET estado = ? WHERE id = ?";
        
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nuevoEstado, PDO::PARAM_STR);
            $consulta -> bindValue(2, $id, PDO::PARAM_INT);
            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al modificar elemento: ".$e->getMessage();
        }

    }

    /**
     * `BorrarUsuario` se encarga de eliminar un registro 
     * de la tabla `usuarios` en una base de datos. 
     * 
     * @param mixed $id
     * 
     * @return [type]
     * 
     */
    static public function BorrarUsuario($id)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "DELETE FROM usuarios WHERE id = ?";
        
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

    /**
     * La funcion VerificarUsuarioYContraseña() se utiliza para comparar la clave dada para un usuario y su hash almacenado en la base de datos
     * con el finde verificar que el usuario que se esta intentando logear sea el correcto.
     *
     * @param mixed $nombreUsuario
     * @param mixed $password
     * 
     * @return [type]
     * 
     */
    static public function VerificarUsuarioYContraseña($nombreUsuario,$password)
    {
        $pdo = AccederABaseDeDatos('comanda');
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        try
        {   
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(1, $nombreUsuario, PDO::PARAM_INT);
            $consulta -> execute();

            $consulta -> setFetchMode(PDO::FETCH_CLASS,'Usuario');
            $usuario = $consulta -> fetch();


            foreach($usuario as $key => $value)
            {
                switch($key)
                {
                    case "contraseña":
                        $hash = $value;
                        break;                                       
                }
            }   
            

            $contrasenaValida = password_verify($password,$hash);
            

            if($usuario && $contrasenaValida)
            {
                return $usuario;
            }
            else
            {
                return NULL;
            }
        }
        catch(PDOException $e)
        {
            echo "Error al buscar usuario: ".$e->getMessage();
        }
    }  
    
} 





