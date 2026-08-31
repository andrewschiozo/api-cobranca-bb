<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers;

use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\BBApiException;
use Exception;

/**
 * Serviço responsável por interpretar o JSON de erro padrão da API do BB.
 */
class ErrorResponseParser
{
    /**
     * @param int $httpCode Código HTTP (400, 500, etc.)
     * @param string $errorJson JSON de erro retornado pela API.
     * @throws \AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\BBApiException
     */
    public function parse(int $httpCode, string $errorJson): void
    {
        try {
            $data = json_decode($errorJson, true, 10, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new Exception("A resposta da API não é um JSON válido: {$errorJson}", $httpCode);
        }

        if (
            !isset($data['erros']) &&
            !isset($data['error']) &&
            !isset($data['errors']) &&
            !isset($data['detail'])
        ) {
            throw new Exception("Não há um tratamento para o erro retornado pela API: {$errorJson}", $httpCode);
        }

        $mensagemDetalhada = '';
        if (isset($data['erros'])) {
            foreach($data['erros'] as $erro) {
                $mensagemDetalhada .= $erro['mensagem'] . "\n";
            }
        }
        if (isset($data["error"])) {
            $detail = isset($data["message"]) ? $data["message"] : '';
            $detail .= isset($data["error_description"]) ? $data["error_description"] : '';
            $detail = empty($detail) ? 'Verifique o error parser, a API pode ter enviado um novo formato de resposta' : $detail;
            $mensagemDetalhada .= $data["error"] . ": " . $detail;
        }

        if (isset($data['errors'])) {
            foreach($data['errors'] as $erro) {

                $mensagemDetalhada .= isset($erro['message']) ? 'Message: ' . $erro['message'] . '. ' : '';
                $mensagemDetalhada .= isset($erro['code']) ? 'Code: ' . $erro['code'] . '. '  : '';
                $mensagemDetalhada .= isset($erro['title']) ? 'Title: ' . $erro['title'] . '. '  : '';
                $mensagemDetalhada .= isset($erro['detail']) ? 'Detail: ' . $erro['detail'] . '. '  : '';
                $mensagemDetalhada = empty($mensagemDetalhada) ? 'Verifique o error parser, a API pode ter enviado um novo formato de resposta' : $mensagemDetalhada;
                $mensagemDetalhada .= "\n";
            }
        }

        if (isset($data['detail'])) {
            $mensagemDetalhada .= $data['detail'] . "\n";
        }

        throw new Exception($mensagemDetalhada, $httpCode);
    }
}