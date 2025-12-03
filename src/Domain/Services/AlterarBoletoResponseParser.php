<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services;

use AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface;

/**
 * Serviço responsável por receber a resposta JSON da API do BB e 
 * transformá-la em um formato de dados internos limpo e confiável.
 */
class AlterarBoletoResponseParser implements ResponseParserInterface
{
    /**
     * Transforma o JSON de resposta da API em um array de dados limpos.
     * 
     * @param string $jsonResponse JSON bruto retornado pela API.
     * @return array Dados estruturados da cobrança.
     * @throws \JsonException Se o JSON for inválido.
     */
    public function parse(string $jsonResponse): array
    {
        $data = json_decode($jsonResponse, true, 512, JSON_THROW_ON_ERROR);

        return [
	        'numeroContratoCobranca' => $data['numeroContratoCobranca'] ?? null,
            'dataAtualizacao' => $data['dataAtualizacao'] ?? null,
            'horarioAtualizacao' => $data['horarioAtualizacao'] ?? null
        ];
    }
}