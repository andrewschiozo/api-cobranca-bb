<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters;

use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Ports\HttpClientInterface;
use InvalidArgumentException;

class BBHttpClientAdapter implements HttpClientInterface
{
    private ?string $token = null;
    private ?string $appKey = null;

    public function __construct(
        private ErrorResponseParser $errorParser,
        private HttpClientInterface $client,
    ) {
    }

    public function getRawClient(): HttpClientInterface
    {
        return $this->client;
    }

    public function withAuth(string $token, string $appKey): self 
    {
        $clone = clone $this;
        $clone->token = $token;
        $clone->appKey = $appKey;
        return $clone;
    }

    public function sendRequest(string $method, string $uri, array $options = []): string
    {
        if (!$this->token || !$this->appKey) {
            throw new InvalidArgumentException("Token e AppKey são obrigatórios.");
        }

        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => 'Bearer ' . $this->token,
            'X-Application-Key' => $this->appKey,
        ]);

        try {
            return $this->client->sendRequest($method, $uri, $options);
        } catch (HttpCommunicationException $e) {
            $this->errorParser->parse($e->getCode(), $e->getResponseBody());
            throw $e;
        }
    }
    public function post(string $uri, array $payload, array $headers = []): string
    {
        return $this->sendRequest('POST', $uri, [
            'json' => $payload,
            'headers' => $headers
        ]);
    }
    public function get(string $uri, array $queryParams = [], array $headers = []): string
    {
        return $this->sendRequest('GET', $uri, [
            'query' => $queryParams,
            'headers' => $headers
        ]);
    }
    public function put(string $uri, array $payload, array $headers = []): string
    {
        return $this->sendRequest('PUT', $uri, [
            'json' => $payload,
            'headers' => $headers
        ]);
    }
    public function patch(string $uri, array $payload, array $headers = []): string
    {
        return $this->sendRequest('PATCH', $uri, [
            'json' => $payload,
            'headers' => $headers
        ]);
    }
}
