<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

class AutenticarDTO
{
    public function __construct(
        public readonly ?string $authUrl,
        public readonly ?string $clientId,
        public readonly ?string $clientSecret,
        public readonly ?string $scope,
    ) {}

    /**
     * Cria uma instância de AutenticarDTO a partir de um array de dados.
     * @param array $data
    * [
    *      'authUrl': 'https://api.bb.com.br/oauth/token',
    *      'clientId': 'client-id',
    *      'clientSecret': 'client-secret',
    *      'appKey': 'app-key',
    *      'accessToken': 'access-token'
    * ]
     * @return AutenticarDTO
     */
    public static function fromArray(array $data): AutenticarDTO
    {
        return new self(
            authUrl: $data['authUrl'] ?? null,
            clientId: $data['clientId'] ?? null,
            clientSecret: $data['clientSecret'] ?? null,
            scope: $data['scope'] ?? null,
        );
    }
}
