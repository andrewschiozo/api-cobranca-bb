<?php

declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Ports\HttpClientInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class GuzzleHttpClientAdapter implements HttpClientInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private ClientInterface $client,
    ) {
    }

    public function sendRequest(string $method, string $uri, array $options = []): string
    {
        $this->logger->debug(
            "GuzzleHttpClientAdapter Request: {$method} -> {$uri}.",
            [
                'method' => $method,
                'uri' => $uri,
                'options' => $options
            ]
        );

        try {
            $response = $this->client->request($method, $uri, $options);

            $responseBody = $response->getBody()->getContents();

            $this->logger->info(
                "GuzzleHttpClientAdapter Response: {$method} -> {$uri}.",
                [
                    'status' => $response->getStatusCode(),
                    'response_snippet' => substr($responseBody, 0, 500)
                ]
            );

            return $responseBody;

        } catch (RequestException $e) {
            $httpCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $this->logger->error(
                "Falha na requisição.",
                [
                    'class' => __CLASS__,
                    'method' => $method,
                    'uri' => $uri,
                    'error' => $e->getMessage(),
                    'response' => $responseBody,
                    'http_code' => $httpCode
                ]
            );

            throw new HttpCommunicationException(
                $e->getMessage(),
                $e->getCode(),
                $responseBody,
                $e
            );
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
