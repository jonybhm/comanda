<?php
require_once '../vendor/autoload.php';
include_once "./manejadores/PedidoProductoManejador.php";
include_once "./manejadores/UsuarioManejador.php";
include_once "./manejadores/ProductoManejador.php";
include_once "./manejadores/MesaManejador.php";
include_once "./manejadores/PedidoManejador.php";
include_once "./manejadores/TokenManejador.php";

require_once "./middlewares/AuthMiddleware.php";
require_once "./token/JasonWebToken.php";


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

use FastRoute\RouteCollector;
//use GuzzleHttp\Psr7\Request;
use Slim\Psr7\Request;
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
    $group->post('[/]', \PedidoProductoManejador::class . ':TomarPedido');
})->add(new AuthMiddleware(["mozo"]));


#---------------------------RECIBO DE PEDIDOS EN COCINA/BARRA---------------------------

$app->group('/reciboPedidos',function(RouteCollectorProxy $group)
{
    $group->get('[/]', \PedidoProductoManejador::class . ':RecibirPedidosPendientes');
    $group->put('/{idPedidoProducto}', \PedidoProductoManejador::class . ':ModificarPedidosPendientes');    
})->add(new AuthMiddleware(["cocinero","bartender","cervecero"]));


#---------------------------ENTREGA PEDIDO EN MESA---------------------------

$app->group('/entregaPedidos',function(RouteCollectorProxy $group)
{
    $group->get('[/]', \PedidoProductoManejador::class . ':RecibirPedidosListosParaEntregar');
    $group->put('/{idPedidoProducto}', \PedidoProductoManejador::class . ':ServirPedido');
})->add(new AuthMiddleware(["mozo"]));


#---------------------------COBRO DE PEDIDOS A CLIENTES---------------------------

$app->group('/cobroPedidos',function(RouteCollectorProxy $group)
{
    $group->put('/{idPedido}', \PedidoProductoManejador::class . ':CobrarPedido');
})->add(new AuthMiddleware(["mozo"]));


#---------------------------CIERRE DE MESA---------------------------

$app->group('/cierrePedidos',function(RouteCollectorProxy $group)
{
    $group->put('/{id}', \PedidoProductoManejador::class . ':CerrarMesa');
})->add(new AuthMiddleware(["socio"]));


#---------------------------TOKENS---------------------------


$app->group('/login',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \TokenManejador::class . ':IngresarYGenerarToken');
});

$app->run();

#---------------------------ARCHIVOS---------------------------


$app->group('/archivos',function(RouteCollectorProxy $group)
{
    $group->post('/importar', \UsuarioManejador::class . ':Importar');
    $group->get('/exportar', \UsuarioManejador::class . ':Exportar');
});

$app->run();

/*
token socio ejemplo:

eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MTg1OTI1OTIsImV4cCI6MTcxODY1MjU5MiwiYXVkIjoiOTNjMmMzZGYzODVkYWE5OGEwNDdkMDlmNTBiOGU1ZmEzOTk2ODg2MyIsImRhdGEiOnsidXN1YXJpbyI6InBydWViYSIsInBlcmZpbCI6InNvY2lvIn0sImFwcCI6IlRlc3QgSldUIn0.G6qB_TDLsP9KQJE7anyUIfV_BEFhIi8pwOwoD73Qik4

token mozo ejemplo:

eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MTg1OTI3NDEsImV4cCI6MTcxODY1Mjc0MSwiYXVkIjoiOTNjMmMzZGYzODVkYWE5OGEwNDdkMDlmNTBiOGU1ZmEzOTk2ODg2MyIsImRhdGEiOnsidXN1YXJpbyI6InBydWViYSIsInBlcmZpbCI6Im1vem8ifSwiYXBwIjoiVGVzdCBKV1QifQ.1v9-t1L9-sV9gXpAHw2-Kj5JYkXC55ysy2VdXSkOWp4
*/
?>

