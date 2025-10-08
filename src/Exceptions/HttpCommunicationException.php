<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Exceptions;

/**
 * Exceção genérica para encapsular qualquer falha de comunicação HTTP
 * (rede, timeout, erro 4xx/5xx da API do BB, etc.).
 */
class HttpCommunicationException extends \RuntimeException
{
    /**
     * @param string $message Mensagem de erro.
     * @param int $code Código HTTP ou código de erro interno.
     * @param \Throwable|null $previous A exceção original (ex: GuzzleException) que causou a falha.
     */
    public function __construct(
        string $message = "Falha na comunicação com a API.",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
