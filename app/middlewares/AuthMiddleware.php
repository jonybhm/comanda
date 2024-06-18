<?php
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Psr7\Response;
use Slim\Psr7\Response as ResponseClass;

require_once '../vendor/autoload.php';
require_once "./token/JasonWebToken.php";


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

        $header = $request->getHeaderLine('Authorization');

        if($header)
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {
            $token ='';
        }

        try 
        {
            JasonWebToken::VerificarToken($token);

            $data = JasonWebToken::ObtenerData($token);
            
            if(in_array($data->perfil,$this->_perfiles))
            {
                $response = $requestHandler->handle($request);
            }
            else
            {
                $response = new ResponseClass();
                $payload = json_encode(array('mensaje' => "No es ". $this->_perfiles[0]));
                $response->getBody()->write($payload);                
            }         
           
        } 
        catch (Exception $e) 
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'.PHP_EOL.$e));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    
}