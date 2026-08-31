<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\Enums;

enum DescontoTipoEnum: int
{
    case SEM_DESCONTO = 0;
    case VALOR_FIXO_ATE_DATA = 1;
    case PERCENTUAL_ATE_DATA = 2;
    case DESCONTO_POR_DIA_ANTICIPACAO = 3;

    public static function tryFromString(string $value): ?DescontoTipoEnum
    {
        return match ($value) {
            'SEM_DESCONTO' => self::SEM_DESCONTO,
            'VALOR_FIXO' => self::VALOR_FIXO_ATE_DATA,
            'PERCENTUAL' => self::PERCENTUAL_ATE_DATA,
            'DIA_ANTICIPACAO' => self::DESCONTO_POR_DIA_ANTICIPACAO,
            default => null,
        };
    }
}
