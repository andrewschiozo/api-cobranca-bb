<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Ports;

use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use Psr\Log\LoggerInterface;

/**
 * Interface que define o contrato para comunicação HTTP com APIs externas.
 */
interface HttpClientInterface 
{
    /**
     * @param string $uri A URI relativa da API do BB (ex: /cobrancas/v2/boletos)
     * @param array $payload O corpo da requisição POST (JSON)
     * @param array $headers Headers adicionais, se necessários.
     * @return string O corpo da resposta da API (JSON puro)
     * @throws HttpCommunicationException Se houver falha de conexão, timeout, ou erro 5xx/4xx.
     */
    public function post(string $uri, array $payload, array $headers = [], ?LoggerInterface $requestLogger = null): string;
    
    /**
     * @param string $uri A URI relativa da API do BB (ex: /cobrancas/v2/boletos/12345)
     * @param array $queryParams Parâmetros da query string.
     * @param array $headers Headers adicionais, se necessários.
     * @return string O corpo da resposta da API (JSON puro)
     * @throws HttpCommunicationException Se houver falha de conexão, timeout, ou erro 5xx/4xx.
     */
    public function get(string $uri, array $queryParams = [], array $headers = [], ?LoggerInterface $requestLogger = null): string;

    /**
     * @param string $uri Endpoint do recurso
     * @param array $payload Corpo da requisição (JSON)
     * @param array $headers
     * @param LoggerInterface|null $requestLogger
     */
    public function put(string $uri, array $payload, array $headers = [], ?LoggerInterface $requestLogger = null): string;
}