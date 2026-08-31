<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Infrastructure\Adapters;

use AndrewsChiozo\ApiCobrancaBB\Domain\DTOs\Responses\BBHttpClientAuditoria;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\Ports\HttpClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class BBHttpClientAdapter implements HttpClientInterface
{
    private ?string $token = null;
    private ?string $appKey = null;
    private BBHttpClientAuditoria $lastAudit;

    public function __construct(
        private ErrorResponseParser $errorParser,
        private ClientInterface $client,
    ) {
    }

    public function withAuth(string $token, string $appKey): self 
    {
        $clone = clone $this;
        $clone->token = $token;
        $clone->appKey = $appKey;
        return $clone;
    }

    public function lastAudit(): BBHttpClientAuditoria
    {
        return $this->lastAudit;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }

    private function request(string $method, string $uri, string $payload, array $headers = []): string
    {
        if (!empty($this->token) && !empty($this->appKey)) {
            $headers = array_merge($options['headers'] ?? [], [
                'Authorization' => 'Bearer ' . $this->token,
                'X-Application-Key' => $this->appKey,
            ]);
        }

        $requestInfo = [
            'method' => $method,
            'uri' => $uri,
            'headers' => $headers,
            'payload' => $payload
        ];

        $request = new Request($method, $uri, $headers, $payload);

        try {
            $response = $this->client->sendRequest($request);
            $responseBody = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            if ($statusCode >= 400) {
                $this->errorParser->parse($statusCode, $responseBody);
            }

            $requestInfo['success'] = true;
            $requestInfo['statusCode'] = $response->getStatusCode();
            $requestInfo['response'] = $responseBody;
            $this->lastAudit = BBHttpClientAuditoria::fromArray($requestInfo);

            return $responseBody;
        } catch (BBApiException | ClientException | NetworkExceptionInterface | Throwable $e) {

            $requestInfo['success'] = false;
            $requestInfo['statusCode'] = $e->getCode();
            $requestInfo['response'] = $e->getMessage();
            $this->lastAudit = BBHttpClientAuditoria::fromArray($requestInfo);
            throw new BBApiException(
                'Erro Integração API BB',
                $this->lastAudit->statusCode,
                $this->lastAudit
            );
        }
    }

    public function get(string $uri, array $queryParams = [], array $headers = []): string
    {
        if (!empty($queryParams)) {
            $uri = $uri . '?' . http_build_query($queryParams);
        };
        return $this->request('GET', $uri, '', $headers);
    }
    public function post(string $uri, array $payload, array $headers = []): string
    {
        return $this->request('POST', $uri, json_encode($payload), $headers);
    }
    public function patch(string $uri, array $payload, array $headers = []): string
    {
        return $this->request('PATCH', $uri, json_encode($payload), $headers);
    }

    public function auth(string $uri, array $payload, array $headers = []): string
    {
        return $this->request('POST', $uri, http_build_query($payload), $headers);
    }
}
