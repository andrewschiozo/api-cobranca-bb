<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\TokenResponseDTO;

class AutenticarResponseParser
{
    public function parse(string $jsonResponse): TokenResponseDTO
    {
        $data = json_decode($jsonResponse, true, 512, JSON_THROW_ON_ERROR);

        return new TokenResponseDTO(
            accessToken: $data['access_token'] ?? '',
            tokenType: $data['token_type'] ?? '',
            expiresIn: $data['expires_in'] ?? 0,
            scope: $data['scope'] ?? ''
        );
    }
}