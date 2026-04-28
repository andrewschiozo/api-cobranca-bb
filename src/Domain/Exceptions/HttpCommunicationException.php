<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions;

/**
 * Exceção genérica para encapsular qualquer falha de comunicação HTTP
 * (rede, timeout, erro 4xx/5xx da API do BB, etc.).
 */
class HttpCommunicationException extends \RuntimeException
{
    private string $responseBody;

    /**
     * @param string $message Mensagem de erro.
     * @param int $code Código HTTP ou código de erro interno.
     * @param string $responseBody Corpo da resposta HTTP.
     * @param \Throwable|null $previous A exceção original (ex: GuzzleException) que causou a falha.
     */
    public function __construct(
        string $message = "Falha na comunicação com a API.",
        int $code = 0,
        string $responseBody = '',
        ?\Throwable $previous = null
    ) {
        $this->responseBody = $responseBody;
        parent::__construct($message, $code, $previous);
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }
}
