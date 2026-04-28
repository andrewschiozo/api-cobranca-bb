<?php

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Http\Controllers;

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoDTO;
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

        $response = $this->cobrancaManager->detalharCobranca($dto);

        return new Psr7Response(
            200, 
            ['Content-Type' => 'application/json'], 
            json_encode($response)
        );
    }

    public function registrar(Request $request): Response
    {
        $dados = json_decode((string) $request->getBody(), true);
        $dados['numeroConvenio'] = $this->numeroConvenio;
        $dados['nossoNumero'] = '2604270101';

        $dto = RegistrarBoletoDTO::fromArray($dados);

        $response = $this->cobrancaManager->registrarCobranca($dto);

        return new Psr7Response(
            201, 
            ['Content-Type' => 'application/json'], 
            json_encode($response)
        );
    }

    public function alterar(Request $request, array $args): Response
    {
        $dados = json_decode((string) $request->getBody(), true);
        $dados['numeroConvenio'] = $this->numeroConvenio;
        $dados['nossoNumero'] = $args['nossoNumero'];

        $dto = AlterarBoletoDTO::fromArray($dados);
        $response = $this->cobrancaManager->alterarCobranca($dto);

        return new Psr7Response(
            200, 
            ['Content-Type' => 'application/json'], 
            json_encode($response)
        );
    }
}