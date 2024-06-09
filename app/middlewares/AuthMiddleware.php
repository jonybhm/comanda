<?php
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Psr7\Response;
use Slim\Psr7\Response as ResponseClass;


class AuthMiddleware
{
    private $_perfil="";

    public function __construct($perfil)
    {
        $this->_perfil = $perfil;
    }

    public function __invoke(IRequest $request, IRequestHandler $requestHandler)
    {
        $response = new ResponseClass();
        echo "entro al authMW";

        $params = $request->getQueryParams();

        if(isset($params["credenciales"]))
        {
            $credenciales = $params ["credenciales"];

            if($credenciales == $this->_perfil)
            {
                $response = $requestHandler ->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(array("error"=>"No es ". $this->_perfil)));
                
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("error"=>"No hay credenciales")));

        }
        echo "salgo del authMW";
        return $response->withHeader('Content-Type','application/json');    
    }
    
}