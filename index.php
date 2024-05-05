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

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE TELAS

$twig = Twig::create(__DIR__ . '/App/Views/Templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->group('/paginas', function (RouteCollectorProxy $group) use ($twig) {
    $group->get('/login', function (Request $request, Response $response, $args) use ($twig) {
        // Renderiza a página de login usando o Twig
        return $twig->render($response, 'login.twig');
    });
})->add(function (Request $request, RequestHandlerInterface $handler) {
    $uri = $request->getUri()->getPath();
    if (!in_array($uri, ['/veterinariaUNESC/paginas/login'])) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["retorno" => false, "mensagem" => 'A requisicao foi efetuada de maneira incorreta.']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});

/////////////////////// ROTAS DE REQUISIÇÕES PARA PROCESSAMENTO DE BACKEND

$app->group('/server', function (RouteCollectorProxy $group) {

    // Definição das rotas dentro do grupo /pessoas

    $group->post('/teste',  App\Controllers\Pessoas::class . ':exibir');
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
