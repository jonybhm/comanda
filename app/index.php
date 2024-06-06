<?php
require_once '../vendor/autoload.php';
include_once "./manejadores/UsuarioManejador.php";
include_once "./manejadores/ProductoManejador.php";
include_once "./manejadores/MesaManejador.php";
include_once "./manejadores/PedidoManejador.php";
require_once "'./middlewares/AuthMiddleware.php'";


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

//middleware
$usuarioMW = function(IRequest $request, IRequestHandler $requestHandler)
{
    $params = $request->getQueryParams();
    
    echo "Entro al middleware".PHP_EOL;

    if(!empty($params))
    {
        $response = $requestHandler ->handle($request);
        
        echo "Salgo del verbo al middleware".PHP_EOL;
        
        return $response;
    }
    else
    {

        echo "NO Entro al verbo".PHP_EOL;
        
        $response = new ResponseClass();
        $response->getBody()->write(json_encode(array("eeror"=>"Parametros incorrectos")));
        return $response->withHeader('Content-Type','application/json');
        
    }
};

//rutas
$app->group('/usuarios',function(RouteCollectorProxy $group)
{
    $group->post('[/]', \UsuarioManejador::class . ':Alta');
    $group->get('/{id}', \UsuarioManejador::class . ':ObtenerUno');
    $group->get('[/]', \UsuarioManejador::class . ':ObtenerTodos');
    $group->put('/{id}', \UsuarioManejador::class . ':Modificar');
    $group->delete('/{id}', \UsuarioManejador::class . ':Baja');
})
->add($usuarioMW)
->add(new AuthMiddleware("socio"));

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