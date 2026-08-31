<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\Enums;

enum MultaTipoEnum: int
{
    case SEM_MULTA = 0;
    case VALOR_FIXO = 1;
    case PERCENTUAL = 2;

    public static function tryFromString(string $value): ?MultaTipoEnum
    {
        return match ($value) {
            'SEM_MULTA' => self::SEM_MULTA,
            'VALOR_FIXO' => self::VALOR_FIXO,
            'PERCENTUAL' => self::PERCENTUAL,
            default => null,
        };
    }
}
