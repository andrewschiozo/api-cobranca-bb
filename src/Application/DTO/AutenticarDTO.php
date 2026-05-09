<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

readonly class AutenticarDTO
{
    public function __construct(
        public string $authUrl,
        public string $clientId,
        public string $clientSecret,
        public string $scope,
    ) {}

    public static function fromArray(array $data): AutenticarDTO
    {
        return new self(
            authUrl: $data['authUrl'],
            clientId: $data['clientId'],
            clientSecret: $data['clientSecret'],
            scope: $data['scope'],
        );
    }
}
