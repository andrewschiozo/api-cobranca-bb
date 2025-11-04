<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services;

use AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface;

class DetalharBoletoResponseParser implements ResponseParserInterface
{
    /**
     * Lida com a transformação do JSON de consulta da API do BB.
     */
    public function parse(string $jsonResponse): array
    {
        $data = json_decode($jsonResponse, true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }
}