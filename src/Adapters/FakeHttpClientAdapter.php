<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Adapters;

use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;

/**
 * Adaptador de Teste para simular respostas da API do BB.
 * Não faz nenhuma requisição HTTP real.
 */
class FakeHttpClientAdapter implements HttpClientInterface
{
    /**
     * @var string Resposta JSON que será retornada por padrão.
     */
    private string $defaultPostResponse = '{"numero_cobranca": "1234567890", "status": "REGISTRADA"}';
    
    /**
     * Permite configurar a resposta que o método POST deve retornar.
     */
    public function setPostResponse(string $response): void
    {
        $this->defaultPostResponse = $response;
    }

    /**
     * Simula uma requisição POST e retorna a resposta configurada.
     */
    public function post(string $uri, array $payload, array $headers = []): string
    {
        return $this->defaultPostResponse;
    }

    /**
     * Simula uma requisição GET.
     */
    public function get(string $uri, array $queryParams = [], array $headers = []): string
    {
        return '{"status": "SUCESSO", "detalhes": "Dados da Cobrança"}';
    }
}