<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Console\Commands;

use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManagerFacade;
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

        $response = $this->cobrancaManager->detalharCobranca($dto);

        return json_encode($response);
    }
}