<?php
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Psr7\Response;
use Slim\Psr7\Response as ResponseClass;


class AuthMiddleware
{
    private $_perfiles=array();

    public function __construct($perfiles)
    {
        $this->_perfiles = $perfiles;
    }

    public function __invoke(IRequest $request, IRequestHandler $requestHandler)
    {
        $response = new ResponseClass();

        $params = $request->getQueryParams();


        if(isset($params["credenciales"]))
        {
            $credenciales = $params ["credenciales"];

            if(in_array($credenciales,$this->_perfiles))
            {
                $response = $requestHandler ->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(array("error"=>"No es ". $this->_perfiles[0])));
                
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("error"=>"No hay credenciales")));

        }
        return $response->withHeader('Content-Type','application/json');    
    }
    
}