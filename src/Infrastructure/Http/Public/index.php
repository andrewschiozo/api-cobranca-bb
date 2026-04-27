<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Dotenv\Dotenv;
use FastRoute\Dispatcher;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;

// 1. Carrega variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__ . '\..\..\..\..');
$dotenv->load();

// 2. Inicializa Container e Router
$container = require __DIR__ . '\..\container.php';
$dispatcher = require __DIR__ . '\..\routes.php';

// 3. Cria a Request PSR-7
$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
$request = $creator->fromGlobals();

// 4. Roteamento
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        $response = new Response(404, [], 'Not Found');
        break;
        
    case Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response(405, [], 'Method Not Allowed');
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1]; // [BoletoController, 'metodo']
        $vars = $routeInfo[2];    // parâmetros da URL ({nossoNumero})

        // 5. O Container resolve a Controller e suas dependências (Facade, UseCases, etc)
        $controller = $container->get($handler[0]);
        $method = $handler[1];

        // Executa a controller
        $response = $controller->$method($request, $vars);
        break;
}

// 6. Emitter (Envia a Response PSR-7 para o browser)
(new SapiEmitter())->emit($response);