<?php 

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class SessionMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Verifique a sessão usando a classe UserSessao
        if (!\App\Helpers\Sessao::verificaSessao()) {
            $response = new SlimResponse();
            return $response->withHeader('Location', '/veterinaria/paginas/login')->withStatus(302);
        }

        return $handler->handle($request);
    }
}