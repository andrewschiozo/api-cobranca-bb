<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Application\DTO;

use InvalidArgumentException;

class RegistrarBoletoDTO
{
    public function __construct(
        public readonly string $numeroConvenio,
        public readonly string $dataVencimento,
        public readonly string $valorTitulo,
        public readonly string $nossoNumero,
        public readonly string $pagadorNumeroDocumento,
        public readonly string $pagadorCep,
        public readonly ?string $pagadorNome,
        public readonly ?string $pagadorEndereco,
        public readonly ?string $pagadorCidade,
        public readonly ?string $pagadorBairro,
        public readonly ?string $pagadorUf,
        public readonly ?string $pagadorTelefone,
        public readonly ?string $pagadorEmail,
        public readonly ?string $dataEmissao,
        public readonly ?string $valorAbatimento,
        public readonly ?string $numeroTituloBeneficiario,
        public readonly ?string $desconto1Tipo,
        public readonly ?string $desconto1Data,
        public readonly ?string $desconto1Valor,
        public readonly ?string $desconto2Tipo,
        public readonly ?string $desconto2Data,
        public readonly ?string $desconto2Valor,
        public readonly ?string $desconto3Tipo,
        public readonly ?string $desconto3Data,
        public readonly ?string $desconto3Valor,
        public readonly ?string $jurosMoraTipo,
        public readonly ?string $jurosMoraValor,
        public readonly ?string $multaTipo,
        public readonly ?string $multaValor,
        public readonly ?string $multaData,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            numeroConvenio: $data['numeroConvenio'] ?? throw new InvalidArgumentException('Convênio não informado'),
            dataVencimento: $data['dataVencimento'] ?? throw new InvalidArgumentException('Data de vencimento não informada'),
            valorTitulo: $data['valorTitulo'] ?? throw new InvalidArgumentException('Valor do título não informado'),
            nossoNumero: $data['nossoNumero'] ?? throw new InvalidArgumentException('Nosso número não informado'),
            pagadorNumeroDocumento: $data['pagadorNumeroDocumento'] ?? throw new InvalidArgumentException('Documento do pagador não informado'),
            pagadorCep: $data['pagadorCep'] ?? null,
            pagadorNome: $data['pagadorNome'] ?? null,
            pagadorEndereco: $data['pagadorEndereco'] ?? null,
            pagadorCidade: $data['pagadorCidade'] ?? null,
            pagadorBairro: $data['pagadorBairro'] ?? null,
            pagadorUf: $data['pagadorUf'] ?? null,
            pagadorTelefone: $data['pagadorTelefone'] ?? null,
            pagadorEmail: $data['pagadorEmail'] ?? null,
            dataEmissao: $data['dataEmissao'] ?? null,
            valorAbatimento: $data['valorAbatimento'] ?? null,
            numeroTituloBeneficiario: $data['numeroTituloBeneficiario'] ?? null,
            desconto1Tipo: $data['desconto1Tipo'] ?? null,
            desconto1Data: $data['desconto1Data'] ?? null,
            desconto1Valor: $data['desconto1Valor'] ?? null,
            desconto2Tipo: $data['desconto2Tipo'] ?? null,
            desconto2Data: $data['desconto2Data'] ?? null,
            desconto2Valor: $data['desconto2Valor'] ?? null,
            desconto3Tipo: $data['desconto3Tipo'] ?? null,
            desconto3Data: $data['desconto3Data'] ?? null,
            desconto3Valor: $data['desconto3Valor'] ?? null,
            jurosMoraTipo: $data['jurosMoraTipo'] ?? null,
            jurosMoraValor: $data['jurosMoraValor'] ?? null,
            multaTipo: $data['multaTipo'] ?? null,
            multaValor: $data['multaValor'] ?? null,
            multaData: $data['multaData'] ?? null
        );
    }
}
