<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/veterinariaUNESC');
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$twig = Twig::create(__DIR__ . '/App/Views/paginas', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE TELAS

$app->group('/paginas', function (RouteCollectorProxy $group) use ($twig) {

    $group->get('/login', function (Request $request, Response $response, $args) use ($twig) {
        $tela =  new App\Views\LoginPage($twig);
        return $tela->exibir($request, $response, $args);
    });


})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, ['/veterinariaUNESC/paginas/login', '/veterinariaUNESC/paginas/outra'])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE BACKEND

$app->group('/server', function (RouteCollectorProxy $group) {

    // Definição das rotas dentro do grupo /pessoas

    $group->get('/teste',  App\Controllers\Pessoas::class . ':exibir');
})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, ['/veterinariaUNESC/server/teste'])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisição foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

$app->run();
