<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Enums;

enum JurosMoraTipoEnum: int
{
    case DISPENSAR = 0;
    case VALOR_DIA_ATRASO = 1;
    case TAXA_MENSAL = 2;
    case ISENTO = 3;

    public static function tryFromString(string $value): ?JurosMoraTipoEnum
    {
        return match ($value) {
            'DISPENSAR' => self::DISPENSAR,
            'VALOR_DIA_ATRASO' => self::VALOR_DIA_ATRASO,
            'TAXA_MENSAL' => self::TAXA_MENSAL,
            'ISENTO' => self::ISENTO,
            default => null,
        };
    }
}
