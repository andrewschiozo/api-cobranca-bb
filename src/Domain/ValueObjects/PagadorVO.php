<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

class PagadorVO
{
    
    public function __construct(
        public readonly DocumentoVO $documento,
        public readonly ?string $nome,
        public readonly ?string $endereco,
        public readonly string $cep,
        public readonly ?string $cidade,
        public readonly ?string $bairro,
        public readonly ?string $uf,
        public readonly ?string $telefone,
        public readonly ?string $email,
    ) { }
}