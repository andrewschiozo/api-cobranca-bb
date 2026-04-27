<?php

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Http\Controllers;

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Nyholm\Psr7\Response as Psr7Response;

class BoletoController
{
    public function __construct(
        private CobrancaManagerFacade $cobrancaManager,
        private string $numeroConvenio
    ) {
    }

    public function detalhar(Request $request, array $args): Response
    {
        $dto = DetalharBoletoDTO::fromArray([
            'nossoNumero' => $args['nossoNumero'],
            'numeroConvenio' => $this->numeroConvenio
        ]);

        $dados = $this->cobrancaManager->detalharCobranca($dto);

        return new Psr7Response(
            200, 
            ['Content-Type' => 'application/json'], 
            json_encode($dados)
        );
    }
}