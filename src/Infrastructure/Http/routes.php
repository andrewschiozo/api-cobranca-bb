<?php

use FastRoute\RouteCollector;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Http\Controllers\BoletoController;

return FastRoute\simpleDispatcher(function (RouteCollector $r) {
    $r->addGroup('/api/v1', function (RouteCollector $r) {
        $r->addRoute('GET', '/boleto/{nossoNumero}', [BoletoController::class, 'detalhar']);
        $r->addRoute('POST', '/boleto', [BoletoController::class, 'registrar']);
        $r->addRoute('PATCH', '/boleto/{nossoNumero}', [BoletoController::class, 'alterar']);
    });
});