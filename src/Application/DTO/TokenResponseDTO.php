<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

class TokenResponseDTO
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly int $expiresIn,
        public readonly string $scope
    ) {}

    /**
     * Cria uma instância de TokenResponseDTO a partir de um array de dados.
     * @param array $data
    * [
    *      'accessToken': 'access-token',
    *      'tokenType': 'Bearer',
    *      'expiresIn': 3600,
    *      'scope': 'cobranca.boletos-info cobranca.boletos-requisicao'
    * ]
     * @return TokenResponseDTO
     */
    public static function fromArray(array $data): TokenResponseDTO
    {
        return new self(
            accessToken: $data['accessToken'] ?? null,
            tokenType: $data['tokenType'] ?? null,
            expiresIn: $data['expiresIn'] ?? null,
            scope: $data['scope'] ?? null
        );
    }
}
