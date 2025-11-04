<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\NumeroConvenioInvalidoException;

class NumeroConvenioVO
{
    public function __construct(
        public readonly string $numero
    ){
        if(!preg_match('/^[0-9]+$/', $this->numero)) {
            throw new NumeroConvenioInvalidoException('O número do convenio deve ser numérico.');
        }

        if(strlen($this->numero) !== 7){
            throw new NumeroConvenioInvalidoException('O número do convenio deve ter 7 dígitos.');
        }
    }
}