<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\Enums;

enum DocumentoTipoEnum: int
{
    case CPF = 1;
    case CNPJ = 2;
}