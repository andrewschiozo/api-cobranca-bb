<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Application\DTO;

use InvalidArgumentException;

class AlterarBoletoDTO
{
    public function __construct(
        public readonly string $numeroConvenio,
        public readonly string $nossoNumero,
        public readonly ?string $dataVencimento,
        public readonly ?string $valorTitulo,
    ) {}

    /**
     * Cria uma instância de AlterarBoletoDTO a partir de um array de dados.
     * @param array $data
     * [
     *      'numeroConvenio': '1234567',
     *      'nossoNumero': '1234567890',
     *      'dataVencimento: '2026-12-03',
     *      'valorTitulo: 123.45
     * ]
     * @return AlterarBoletoDTO
     */
    public static function fromArray(array $data): AlterarBoletoDTO
    {
        return new self(
            numeroConvenio: $data['numeroConvenio'] ?? throw new InvalidArgumentException('Convênio não informado'),
            nossoNumero: $data['nossoNumero'] ?? throw new InvalidArgumentException('Nosso número não informado'),
            dataVencimento: $data['dataVencimento'] ?? null,
            valorTitulo: $data['valorTitulo'] ?? null,
        );
    }
}
