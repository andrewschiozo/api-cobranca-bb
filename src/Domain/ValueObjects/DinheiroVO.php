<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\DinheiroInvalidoException;
use InvalidArgumentException;

readonly class DinheiroVO 
{
    private int $centavos;
    private const BASE_CONVERSAO = "100";

    /**
     * @param string $valor Ex.: 99.99
     * @throws InvalidArgumentException
     */
    public function __construct(string $valor) {
        $valorLimpo = trim($valor);

        if (!preg_match('/^-?\d+(?:\.\d{1,2})?$/', $valorLimpo)) {
            throw new DinheiroInvalidoException($valor);
        }

        $this->centavos = (int) bcmul($valorLimpo, self::BASE_CONVERSAO, 0);
    }

    public function isMenorOuIgualAZero(): bool {
        return $this->centavos <= 0;
    }

    public function toString(): string {
        return $this->__toString();
    }

    public function __toString()
    {
        return bcdiv((string)$this->centavos, self::BASE_CONVERSAO, 2);
    }
}