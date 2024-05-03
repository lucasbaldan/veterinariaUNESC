<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/veterinariaUNESC');
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Definição de um grupo de rotas /pessoas
$app->group('/pessoas', function (RouteCollectorProxy $group) {

    // Definição das rotas dentro do grupo /pessoas

    $group->get('/teste',  App\Controllers\Pessoas::class. ':exibir');

})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
     if (!in_array($uri, ['/veterinariaUNESC/pessoas/teste'])) {
         $response = new \Slim\Psr7\Response();
         $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
         return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
     }

    return $handler->handle($request);
});

$app->run();