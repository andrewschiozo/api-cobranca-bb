<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Console\Commands;

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AutenticarDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;

class BoletoCommand
{
    public function __construct(
        private CobrancaManagerFacade $cobrancaManager,
        private string $numeroConvenio
    ) {
    }

    public function detalhar(array $args): string
    {
        $dto = DetalharBoletoDTO::fromArray([
            'nossoNumero' => $args['nossoNumero'],
            'numeroConvenio' => $this->numeroConvenio
        ]);

        $response = $this->cobrancaManager
            ->withAuth($_ENV['BB_COBRANCA_TOKEN'], $_ENV['BB_COBRANCA_APP_KEY'])
            ->detalharCobranca($dto);

        return json_encode($response);
    }

    public function autenticar(array $args): string
    {
        $dto = AutenticarDTO::fromArray([
            'authUrl' => $args['authUrl'],
            'clientId' => $args['clientId'],
            'clientSecret' => $args['clientSecret'],
            'scope' => $args['scope']
        ]);

        $response = $this->cobrancaManager->autenticar($dto);

        return json_encode($response);
    }
}