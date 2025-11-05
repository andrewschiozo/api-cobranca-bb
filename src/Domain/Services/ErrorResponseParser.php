<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException;

/**
 * Serviço responsável por interpretar o JSON de erro padrão da API do BB.
 */
class ErrorResponseParser
{
    /**
     * @param int $httpCode Código HTTP (400, 500, etc.)
     * @param string $errorJson JSON de erro retornado pela API.
     * @throws \AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException
     */
    public function parse(int $httpCode, string $errorJson): void
    {
        try {
            $data = json_decode($errorJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BBApiException("Erro de comunicação: Resposta da API não é um JSON válido.", $httpCode, ['raw_response' => $errorJson]);
        }

        if(!isset($data['erros']) && !isset($data['error'])) {
            throw new BBApiException('Não há um tratamento para o erro retornado pela API.', $httpCode, [ 'json' => $errorJson ]);
        }

        $mensagemDetalhada = '';
        if(isset($data['erros'])) {
            foreach($data['erros'] as $erro) {
                $mensagemDetalhada .= $erro['mensagem'] . "\n";
            }
        }
        if(isset($data["error"])) {
            $mensagemDetalhada .= $data["error"] . ": " . $data["message"];
        }

        throw new BBApiException($mensagemDetalhada, $httpCode, $data);
    }
}