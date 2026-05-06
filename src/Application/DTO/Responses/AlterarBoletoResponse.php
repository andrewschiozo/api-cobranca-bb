<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO\Responses;

readonly class AlterarBoletoResponse
{
    public function __construct(
        public ?int $numeroContratoCobranca,
        public ?string $dataAtualizacao,
        public ?string $horarioAtualizacao
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            numeroContratoCobranca: $data['numeroContratoCobranca'] ?? null,
            dataAtualizacao: $data['dataAtualizacao'] ?? null,
            horarioAtualizacao: $data['horarioAtualizacao'] ?? null
        );
    }
}