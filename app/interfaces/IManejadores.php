<?php

/**
 * La interfaz IManejadores se utiliza para establecer una estructura determinada para los manejadores
 */
interface IManejadores
{
    public function Alta($request, $response, $args);
    public function ObtenerUno($request, $response, $args);
    public function ObtenerTodos($request, $response, $args);
    public function Modificar($request, $response, $args);
    public function Baja($request, $response, $args);
    
}