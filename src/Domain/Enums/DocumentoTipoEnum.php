<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Enums;

enum DocumentoTipoEnum: int
{
    case CPF = 1;
    case CNPJ = 2;
}