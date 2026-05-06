<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\Responses\AlterarBoletoResponse;
use JsonException;

/**
 * Serviço responsável por receber tratar a resposta JSON da API do BB
 */
class AlterarBoletoResponseParser
{
    /**
     * Transforma o JSON de resposta da API em um DTO.
     * 
     * @param string $jsonResponse JSON bruto retornado pela API.
     * @return AlterarBoletoResponse
     * @throws JsonException Se o JSON for inválido.
     */
    public function parse(string $jsonResponse): AlterarBoletoResponse
    {
        $data = json_decode($jsonResponse, true, 512, JSON_THROW_ON_ERROR);

        return new AlterarBoletoResponse(
            numeroContratoCobranca: $data['numeroContratoCobranca'] ?? null,
            dataAtualizacao: $data['dataAtualizacao'] ?? null,
            horarioAtualizacao: $data['horarioAtualizacao'] ?? null
        );
    }
}