<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\Responses\RegistroBoletoResponse;
use JsonException;

/**
 * Serviço responsável por receber e tratar a resposta JSON da API do BB
 */
class RegistrarBoletoResponseParser
{
    /**
     * Transforma o JSON de resposta da API em um array.
     * 
     * @param string $response JSON retornado pela API.
     * @return array
     * @throws JsonException
     */
    public function parse(string $response): array
    {
        return json_decode($response, true, 10, JSON_THROW_ON_ERROR);
    }
}