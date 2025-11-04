<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters;

use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use Psr\Log\LoggerInterface;

/**
 * Adaptador de Mock que serve JSONs de arquivos pré-definidos.
 * Usado em testes unitários/de integração para simular respostas específicas da API.
 */
class MockHttpClientAdapter implements HttpClientInterface
{
    private array $mockResponses = [];

    /**
     * Define a resposta (JSON) que deve ser retornada para uma URI e método específicos.
     * 
     * @param string $method POST, GET, etc.
     * @param string $uri A URI da API (ex: /cobrancas/v2/boletos)
     * @param string $filePath O caminho completo para o arquivo JSON de mock.
     */
    public function addMockResponse(string $method, string $uri, string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Arquivo de mock não encontrado: " . $filePath);
        }
        $this->mockResponses[strtoupper($method)][$uri] = file_get_contents($filePath);
    }

    /**
     * Retorna a resposta JSON configurada para o POST.
     */
    public function post(string $uri, array $payload, array $headers = [], ?LoggerInterface $requestLogger = null): string
    {
        $response = $this->mockResponses['POST'][$uri] ?? null;

        if ($response === null) {
            throw new HttpCommunicationException("Mock POST não configurado para URI: $uri");
        }
        return $response;
    }

    /**
     * Retorna a resposta JSON configurada para o GET.
     */
    public function get(string $uri, array $queryParams = [], array $headers = [], ?LoggerInterface $requestLogger = null): string
    {
        $response = $this->mockResponses['GET'][$uri] ?? null;

        if ($response === null) {
            throw new HttpCommunicationException("Mock GET não configurado para URI: $uri");
        }
        return $response;
    }
}