<?php
require_once '../vendor/autoload.php';
include_once "./manejadores/ServicioManejador.php";
include_once "./manejadores/UsuarioManejador.php";
include_once "./manejadores/ProductoManejador.php";
include_once "./manejadores/MesaManejador.php";
include_once "./manejadores/PedidoManejador.php";
require_once "./middlewares/AuthMiddleware.php";


use FastRoute\RouteCollector;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;


use Slim\Handlers\Strategies\RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response as ResponseClass;



$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();


#============================GENERALES ADMIN============================

#---------------------------USUARIOS---------------------------

$app->group('/usuarios',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \UsuarioManejador::class . ':Alta');
    $group->get('/{id}', \UsuarioManejador::class . ':ObtenerUno');
    $group->get('[/]', \UsuarioManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \UsuarioManejador::class . ':Modificar');
    $group->delete('/{id}', \UsuarioManejador::class . ':Baja');
})->add(new AuthMiddleware(["socio"])); 

#---------------------------PRODUCTOS---------------------------

$app->group('/productos',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \ProductoManejador::class . ':Alta');
    $group->get('/{id}', \ProductoManejador::class . ':ObtenerUno');
    $group->get('[/]', \ProductoManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \ProductoManejador::class . ':Modificar');
    $group->delete('/{id}', \ProductoManejador::class . ':Baja');
})->add(new AuthMiddleware(["socio"]));

#---------------------------MESAS---------------------------

$app->group('/mesas',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \MesaManejador::class . ':Alta');
    $group->get('/{id}', \MesaManejador::class . ':ObtenerUno');
    $group->get('[/]', \MesaManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \MesaManejador::class . ':Modificar');
    $group->delete('/{id}', \MesaManejador::class . ':Baja');
})->add(new AuthMiddleware(["socio"]));

#---------------------------PEDIDOS---------------------------

$app->group('/pedidos',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \PedidoManejador::class . ':Alta');
    $group->get('/{id}', \PedidoManejador::class . ':ObtenerUno');
    $group->get('[/]', \PedidoManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \PedidoManejador::class . ':Modificar');
    $group->delete('/{id}', \PedidoManejador::class . ':Baja');
})->add(new AuthMiddleware(["socio"]));


#============================SERVICIO============================


#---------------------------TOMA DE PEDIDOS---------------------------

$app->group('/tomaPedidos',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \ServicioManejador::class . ':TomarPedido');
})->add(new AuthMiddleware(["mozo"]));


#---------------------------RECIBO DE PEDIDOS EN COCINA/BARRA---------------------------

$app->group('/reciboPedidos',function(RouteCollectorProxy $group)
{
    $group->get('[/]', \ServicioManejador::class . ':RecibirPedidosPendientes');
    $group->put('/{idMesa}', \ServicioManejador::class . ':ModificarPedidosPendientes');    
})->add(new AuthMiddleware(["cocinero","bartender"]));


#---------------------------ENTREGA PEDIDO EN MESA---------------------------

$app->group('/entregaPedidos',function(RouteCollectorProxy $group)
{
    $group->get('[/]', \ServicioManejador::class . ':RecibirPedidosListosParaEntregar');
    $group->put('/{idMesa}', \ServicioManejador::class . ':ServirPedido');
})->add(new AuthMiddleware(["mozo"]));


#---------------------------COBRO DE PEDIDOS A CLIENTES---------------------------

$app->group('/cobroPedidos',function(RouteCollectorProxy $group)
{
    $group->put('/{idMesa}', \ServicioManejador::class . ':CobrarPedido');
})->add(new AuthMiddleware(["mozo"]));


#---------------------------CIERRE DE MESA---------------------------

$app->group('/cierrePedidos',function(RouteCollectorProxy $group)
{
    $group->put('/{id}', \ServicioManejador::class . ':CerrarMesa');
})->add(new AuthMiddleware(["socio"]));

$app->run();

?>
