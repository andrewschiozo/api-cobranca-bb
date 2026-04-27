<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\ValorTituloInvalidoException;

readonly class ValorTituloVO
{
    private DinheiroVO $moeda;

    public function __construct(string $valorRaw) 
    {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $valorRaw)) {
            throw new ValorTituloInvalidoException("Formato de valor inválido. Use '100.50'.");
        }

        $valorObjeto = new DinheiroVO($valorRaw);

        if ($valorObjeto->isMenorOuIgualAZero()) {
            throw new ValorTituloInvalidoException("O título deve ter valor maior que zero.");
        }

        $this->moeda = $valorObjeto;
    }

    public function formatadoParaBB(): string 
    {
        return $this->moeda->__toString();
    }
}