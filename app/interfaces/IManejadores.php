<?php

interface IManejadores
{
    public function Alta($request, $response, $args);
    public function ObtenerUno($request, $response, $args);
    public function ObtenerTodos($request, $response, $args);
    public function Modificar($request, $response, $args);
    public function Baja($request, $response, $args);
    
}