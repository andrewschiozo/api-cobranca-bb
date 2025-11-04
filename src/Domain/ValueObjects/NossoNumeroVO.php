<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\NossoNumeroInvalidoException;

class NossoNumeroVO
{
    public readonly string $nossoNumero;

    public function __construct(string $nossoNumero)
    {
        if(!preg_match('/^[0-9]+$/', $nossoNumero)) {
            throw new NossoNumeroInvalidoException('O nosso número deve ser numérico.');
        }

        if(strlen($nossoNumero) > 10){
            throw new NossoNumeroInvalidoException('O nosso número deve ter no máximo 10 dígitos.');
        }

        $this->nossoNumero = str_pad($nossoNumero,10,'0', STR_PAD_LEFT);
    }
}