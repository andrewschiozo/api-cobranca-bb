<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects;

use AndrewsChiozo\ApiCobrancaBB\Domain\Enums\JurosMoraTipoEnum;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\JurosMoraInvalidoException;

readonly class JurosMoraVO
{
    public JurosMoraTipoEnum $tipo;
    private int $valor;
    private const BASE_CONVERSAO = "100";

    public function __construct(
        JurosMoraTipoEnum $tipo,
        string $valor
    ) {
        $valorLimpo = trim($valor);

        if (!preg_match('/^-?\d+(?:\.\d{1,2})?$/', $valorLimpo)) {
            throw new JurosMoraInvalidoException("O valor do juros de mora não é um formato monetário ou percentual string válido. Valor fornecido: {$valor}");
        }

        $valorInteiro = (int) bcmul($valorLimpo, self::BASE_CONVERSAO, 0);

        if ($valorInteiro < 0) {
            throw new JurosMoraInvalidoException("O valor do juros de mora não pode ser negativo. Valor fornecido: {$valor}");
        }

        if ($tipo === JurosMoraTipoEnum::TAXA_MENSAL && $valorInteiro > 10000) {
            throw new JurosMoraInvalidoException("O valor do juros de mora percentual não pode exceder o limite de 100%. Valor fornecido: {$valor}");
        }

        $this->tipo = $tipo;
        $this->valor = $valorInteiro;
    }

    public function __toString(): string
    {
        return bcdiv((string)$this->valor, self::BASE_CONVERSAO, 2);
    }
}