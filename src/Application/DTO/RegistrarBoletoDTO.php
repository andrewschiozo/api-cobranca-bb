<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

use InvalidArgumentException;

class RegistrarBoletoDTO
{
    public function __construct(
        public readonly string $numeroConvenio,
        public readonly string $dataVencimento,
        public readonly string $valorTitulo,
        public readonly string $nossoNumero,
        public readonly string $pagadorNumeroDocumento,
        public readonly ?string $pagadorNome,
        public readonly ?string $pagadorEndereco,
        public readonly string $pagadorCep,
        public readonly ?string $pagadorCidade,
        public readonly ?string $pagadorBairro,
        public readonly ?string $pagadorUf,
        public readonly ?string $pagadorTelefone,
        public readonly ?string $pagadorEmail
    ) {}
public static function fromArray(array $data): self
    {
        return new self(
            numeroConvenio: $data['numeroConvenio'] ?? throw new InvalidArgumentException('Convênio não informado'),
            dataVencimento: $data['dataVencimento'] ?? throw new InvalidArgumentException('Data de vencimento não informada'),
            valorTitulo: $data['valorTitulo'] ?? throw new InvalidArgumentException('Valor do título não informado'),
            nossoNumero: $data['nossoNumero'] ?? throw new InvalidArgumentException('Nosso número não informado'),
            pagadorNumeroDocumento: $data['pagadorNumeroDocumento'] ?? throw new InvalidArgumentException('Documento do pagador não informado'),
            pagadorNome: $data['pagadorNome'] ?? null,
            pagadorEndereco: $data['pagadorEndereco'] ?? null,
            pagadorCep: $data['pagadorCep'] ?? throw new InvalidArgumentException('Cep do pagador não informado'),
            pagadorCidade: $data['pagadorCidade'] ?? null,
            pagadorBairro: $data['pagadorBairro'] ?? null,
            pagadorUf: $data['pagadorUf'] ?? null,
            pagadorTelefone: $data['pagadorTelefone'] ?? null,
            pagadorEmail: $data['pagadorEmail'] ?? null,
        );
    }
}
