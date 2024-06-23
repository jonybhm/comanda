<?php
require_once '../vendor/autoload.php';
include_once "./manejadores/PedidoProductoManejador.php";
include_once "./manejadores/UsuarioManejador.php";
include_once "./manejadores/ProductoManejador.php";
include_once "./manejadores/EncuestaManejador.php";
include_once "./manejadores/MesaManejador.php";
include_once "./manejadores/PedidoManejador.php";
include_once "./manejadores/TokenManejador.php";
include_once "./manejadores/ArchivoManejador.php";
include_once "./manejadores/EstadisticaManejador.php";


require_once "./middlewares/AuthMiddleware.php";
require_once "./token/JasonWebToken.php";


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();




use FastRoute\RouteCollector;
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
    $group->put('[/]', \UsuarioManejador::class . ':Baja');
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
    $group->post('/tomarFoto', \PedidoProductoManejador::class . ':TomarFoto');
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

$app->group('/cancelarPedido',function(RouteCollectorProxy $group)
{
    $group->put('/{idPedido}', \PedidoProductoManejador::class . ':CancelarPedido');

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


#---------------------------ARCHIVOS---------------------------


$app->group('/producto', function (RouteCollectorProxy $group) {
    $group->post('/importar', \ArchivoManejador::class . ':Importar');
    $group->get('/exportar', \ArchivoManejador::class . ':Exportar');
  })->add(new AuthMiddleware(["socio"]));


#---------------------------ESTADISTICAS---------------------------

$app->group('/estadistica', function (RouteCollectorProxy $group) {
    $group->get('/mesaMasUsada', \EstadisticaManejador::class . ':ObtenerMesaMasUsada');
    $group->get('/mesaMenosUsada', \EstadisticaManejador::class . ':ObtenerMesaMenosUsada');
    $group->get('/mesasMayorFacturacion', \EstadisticaManejador::class . ':ObtenerMesasMayorFacturacion');
    $group->get('/mesasMenorFacturacion', \EstadisticaManejador::class . ':ObtenerMesasMenorFacturacion');
    $group->get('/mesaMayorImporte', \EstadisticaManejador::class . ':ObtenerMesaMayorImporte');
    $group->get('/mesaMenorImporte', \EstadisticaManejador::class . ':ObtenerMesaMenorImporte');
    $group->get('/mesasEntreFechas', \EstadisticaManejador::class . ':ObtenerMesasEntreFechas');
    $group->get('/mejoresComentarios', \EstadisticaManejador::class . ':ObtenerMejoresEncuestas');
    $group->get('/peoresComentarios', \EstadisticaManejador::class . ':ObtenerPeoresEncuestas');
    $group->get('/productosTop', \EstadisticaManejador::class . ':ObtenerPedidoVendidosDescendente');
    $group->get('/productosBottom', \EstadisticaManejador::class . ':ObtenerPedidoVendidosAscendente');
    $group->get('/pedidosTardios', \EstadisticaManejador::class . ':ObtenerPedidoEntregadosFueraDeTiempo');
    $group->get('/pedidosCancelados', \EstadisticaManejador::class . ':ObtenerPedidosCancelados');
    $group->get('/logeos/{nombreUsuario}', \EstadisticaManejador::class . ':ObtenerLogeoUsuarioEspecifico');
    $group->get('/todosUsuarios/{tipoEmpleado}', \EstadisticaManejador::class . ':ObtenerOperacionesUsuarioPorSector');
    $group->get('/{tipoEmpleado}', \EstadisticaManejador::class . ':ObtenerOperacionesUsuarios');
    $group->get('/{tipoEmpleado}/{nombreUsuario}', \EstadisticaManejador::class . ':ObtenerOperacionesUsuarioEspecifico');


})->add(new AuthMiddleware(["socio"]));

#-------------------------CLIENTES--------------------------------
$app->group('/clientes', function (RouteCollectorProxy $group)
{
    $group->post('/encuesta', \EncuestaManejador::class . ':RealizarEncuesta');
    $group->get('/tiempoEspera', \EncuestaManejador::class . ':ObtenerTiempoEspera');
});


$app->run();


?>

