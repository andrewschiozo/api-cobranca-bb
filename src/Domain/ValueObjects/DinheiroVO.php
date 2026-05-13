<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\DinheiroInvalidoException;
use InvalidArgumentException;

readonly class DinheiroVO 
{
    private int $centavos;

    /**
     * @param string $valor Ex.: 99.99
     * @throws InvalidArgumentException
     */
    public function __construct(string $valor) {
        $valorLimpo = trim($valor);

        if (filter_var($valorLimpo, FILTER_VALIDATE_FLOAT) === false) {
            throw new DinheiroInvalidoException($valor);
        }

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