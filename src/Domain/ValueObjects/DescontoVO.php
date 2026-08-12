<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBb\Domain\Enums\DescontoTipoEnum;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\DescontoInvalidoException;
use DateTimeImmutable;

readonly class DescontoVO
{
    public DescontoTipoEnum $tipo;
    public DateTimeImmutable $dataLimite;
    private int $valor;
    private const BASE_CONVERSAO = "100";

    public function __construct(
        DescontoTipoEnum $tipo,
        string $valor,
        DateTimeImmutable $dataLimite,
    ) {
        $valorLimpo = trim($valor);

        if (!preg_match('/^-?\d+(?:\.\d{1,2})?$/', $valorLimpo)) {
            throw new DescontoInvalidoException("O valor do desconto não é um formato monetário ou percentual string válido. Valor fornecido: {$valor}");
        }

        $valorInteiro = (int) bcmul($valorLimpo, self::BASE_CONVERSAO, 0);

        if ($valorInteiro < 0) {
            throw new DescontoInvalidoException("O valor do desconto não pode ser negativo. Valor fornecido: {$valor}");
        }

        if ($tipo === DescontoTipoEnum::PERCENTUAL_ATE_DATA && $valorInteiro > 10000) {
            throw new DescontoInvalidoException("O valor do desconto percentual não pode exceder o limite de 100%. Valor fornecido: {$valor}");
        }

        $this->tipo = $tipo;
        $this->valor = $valorInteiro;
        $this->dataLimite = $dataLimite;
    }

    public function __toString(): string
    {
        return bcdiv((string)$this->valor, self::BASE_CONVERSAO, 2);
    }
}