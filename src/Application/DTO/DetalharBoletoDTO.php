<?php

declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

use InvalidArgumentException;

class DetalharBoletoDTO
{
    public function __construct(
        public string $numeroConvenio,
        public string $nossoNumero
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            numeroConvenio: $data['numeroConvenio'] ?? throw new InvalidArgumentException('Convênio não informado'),
            nossoNumero: $data['nossoNumero'] ?? throw new InvalidArgumentException('Nosso número não informado'),
        );
    }
}
