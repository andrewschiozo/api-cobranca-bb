<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions;

class DinheiroInvalidoException extends \Exception
{
    public function __construct(string $valor)
    {
        parent::__construct("O valor {$valor} não é um formato monetário string válido.");
    }
}
