<?php
require_once '../vendor/autoload.php';
include_once "./manejadores/UsuarioManejador.php";
include_once "./manejadores/ProductoManejador.php";
include_once "./manejadores/MesaManejador.php";
include_once "./manejadores/PedidoManejador.php";

use FastRoute\RouteCollector;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;




$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->group('/usuarios',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \UsuarioManejador::class . ':Alta');
    $group->get('/{id}', \UsuarioManejador::class . ':ObtenerUno');
    $group->get('[/]', \UsuarioManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \UsuarioManejador::class . ':Modificar');
    $group->delete('/{id}', \UsuarioManejador::class . ':Baja');
});

$app->group('/productos',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \ProductoManejador::class . ':Alta');
    $group->get('/{id}', \ProductoManejador::class . ':ObtenerUno');
    $group->get('[/]', \ProductoManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \ProductoManejador::class . ':Modificar');
    $group->delete('/{id}', \ProductoManejador::class . ':Baja');
});

$app->group('/mesas',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \MesaManejador::class . ':Alta');
    $group->get('/{id}', \MesaManejador::class . ':ObtenerUno');
    $group->get('[/]', \MesaManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \MesaManejador::class . ':Modificar');
    $group->delete('/{id}', \MesaManejador::class . ':Baja');
});

$app->group('/pedidos',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \PedidoManejador::class . ':Alta');
    $group->get('/{id}', \PedidoManejador::class . ':ObtenerUno');
    $group->get('[/]', \PedidoManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \PedidoManejador::class . ':Modificar');
    $group->delete('/{id}', \PedidoManejador::class . ':Baja');
});


$app->run();

?>