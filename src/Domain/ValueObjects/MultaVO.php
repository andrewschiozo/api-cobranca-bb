<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Enums\MultaTipoEnum;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\MultaInvalidoException;
use DateTimeImmutable;

readonly class MultaVO
{
    public MultaTipoEnum $tipo;
    private int $valor;
    public DateTimeImmutable $data;
    private const BASE_CONVERSAO = "100";

    public function __construct(
        MultaTipoEnum $tipo,
        string $valor,
        DateTimeImmutable $data
    ) {
        $valorLimpo = trim($valor);

        if (!preg_match('/^-?\d+(?:\.\d{1,2})?$/', $valorLimpo)) {
            throw new MultaInvalidoException("O valor da multa não é um formato monetário ou percentual string válido. Valor fornecido: {$valor}");
        }

        $valorInteiro = (int) bcmul($valorLimpo, self::BASE_CONVERSAO, 0);

        if ($valorInteiro < 0) {
            throw new MultaInvalidoException("O valor da multa não pode ser negativo. Valor fornecido: {$valor}");
        }

        if ($tipo === MultaTipoEnum::PERCENTUAL && $valorInteiro > 10000) {
            throw new MultaInvalidoException("O valor da multa percentual não pode exceder o limite de 100%. Valor fornecido: {$valor}");
        }

        $this->tipo = $tipo;
        $this->valor = $valorInteiro;
        $this->data = $data;
    }

    public function __toString(): string
    {
        return bcdiv((string)$this->valor, self::BASE_CONVERSAO, 2);
    }
}