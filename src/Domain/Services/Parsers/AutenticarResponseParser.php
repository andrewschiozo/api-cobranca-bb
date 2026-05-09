<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers;

use AndrewsChiozo\ApiCobrancaBb\Domain\DTOs\Responses\TokenResponseDTO;
use JsonException;

/**
 * Serviço responsável por receber e tratar a resposta JSON da API do BB
 */
class AutenticarResponseParser
{
    /**
     * Transforma o JSON de resposta da API em um DTO.
     * @param string $response JSON retornado pela API.
     * @return TokenResponseDTO
     * @throws JsonException
     */
    public function parse(string $response): TokenResponseDTO
    {
        $data = json_decode($response, true, 10, JSON_THROW_ON_ERROR);

        return new TokenResponseDTO(
            accessToken: $data['access_token'],
            tokenType: $data['token_type'],
            expiresIn: $data['expires_in'],
            scope: $data['scope']
        );
    }
}