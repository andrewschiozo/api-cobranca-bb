<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\DTOs\Responses;

readonly class TokenResponseDTO
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
        public string $scope
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: $data['accessToken'],
            tokenType: $data['tokenType'],
            expiresIn: $data['expiresIn'],
            scope: $data['scope']
        );
    }
}
