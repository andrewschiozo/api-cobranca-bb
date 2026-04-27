<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\ValorTituloInvalidoException;

readonly class DinheiroVO 
{
    private int $centavos;

    public function __construct(string $valor) {
        // Converte string "100.50" para int 10050
        $this->centavos = (int) bcmul($valor, "100", 0);
    }

    public function isMenorOuIgualAZero(): bool {
        return $this->centavos <= 0;
    }

    public function toString(): string {
        return $this->__toString();
    }

    public function __toString()
    {
        return bcdiv((string)$this->centavos, "100", 2);
    }
}