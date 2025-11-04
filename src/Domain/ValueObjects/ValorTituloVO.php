<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\ValorTituloInvalidoException;

class ValorTituloVO
{
    public readonly float $valor;

    public function __construct(string $valorTitulo) {

        if (!filter_var($valorTitulo, FILTER_VALIDATE_FLOAT)) {
            throw new ValorTituloInvalidoException('O valor do título deve ser um número real.');
        }

        if ($valorTitulo <= 0) {
            throw new ValorTituloInvalidoException('O valor do título deve ser maior que zero.');
        }

        $this->valor = floatval($valorTitulo);
    }

}