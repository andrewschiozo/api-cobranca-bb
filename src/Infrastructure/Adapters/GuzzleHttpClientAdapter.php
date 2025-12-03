<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters;

use AndrewsChiozo\ApiCobrancaBb\Domain\Services\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\TokenStorageInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class GuzzleHttpClientAdapter implements HttpClientInterface
{
    private ClientInterface $client;
    private string $baseUrl;
    private string $authUrl;
    private string $clientId;
    private string $clientSecret;
    private string $appKey;

    private ErrorResponseParser $errorParser;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        array $options,
        ErrorResponseParser $errorParser,
        TokenStorageInterface $tokenStorage,
        ?ClientInterface $client = null,
    ) {
        $this->baseUrl = $options['baseUrl'];
        $this->authUrl = $options['authUrl'];
        $this->clientId = $options['clientId'];
        $this->clientSecret = $options['clientSecret'];
        $this->appKey = $options['appKey'];

        $this->errorParser = $errorParser;
        $this->tokenStorage = $tokenStorage;
        
        //ssl desabilitado p/ testes
        $this->client = $client ?? new Client(['base_uri' => $this->baseUrl, 'verify' => false]);
    }

    private function getAccessToken(LoggerInterface $logger): ?string
    {
        $cachedToken = $this->tokenStorage->getToken();
        if (null !== $cachedToken) {
            $logger->info('Token obtido do cache');
            return $cachedToken;
        }

        try {
            $response = $this->client->request('POST', $this->authUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    //'scope' => 'cobrancas.registro-boleto',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $accessToken = $data['access_token'];
            $expiresIn = $data['expires_in'];
            $this->tokenStorage->saveToken($accessToken, $expiresIn);

            $logger->info('API BB: Token obtido da autenticação.');

            return $accessToken;

        } catch (RequestException $e) {
            $logger->critical('API BB: Falha ao obter token.', ['exception' => $e->getMessage()]);
            throw new HttpCommunicationException('Falha na autenticação com o Banco do Brasil: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    private function sendRequest(string $method, string $uri, array $options, LoggerInterface $requestLogger): string
    {
        $requestLogger->debug("API BB: {$method} -> {$uri}.", [
            'method' => $method,
            'uri' => $uri,
            'options' => $options
        ]);

        try {            
            $token = $this->getAccessToken($requestLogger);

            $options['headers']['Authorization'] = 'Bearer ' . $token;
            $options['headers']['X-Application-Key'] = $this->appKey;

            $response = $this->client->request($method, $uri, $options);

            $responseBody = $response->getBody()->getContents();

            $requestLogger->info("API BB: Resposta {$method} {$uri} recebida com sucesso.", [
                'status' => $response->getStatusCode(),
                'response_snippet' => substr($responseBody, 0, 500) // Loga apenas um trecho da resposta
            ]);

            return $responseBody;

        } catch (RequestException $e) {
            $httpCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $requestLogger->error('API BB: Falha na requisição.', [
                'method' => $method,
                'uri' => $uri,
                'error' => $e->getMessage(),
                'response' => $responseBody
            ]);

            if ($responseBody) {
                $this->errorParser->parse($httpCode, $responseBody);
            }

            throw new HttpCommunicationException('Erro na requisição para a API do BB: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function post(string $uri, array $payload, array $headers = [], ?LoggerInterface $requestLogger = null): string
    {
        return $this->sendRequest('POST', $uri, [
            'json' => $payload,
            'headers' => $headers
        ], $requestLogger);
    }

    public function get(string $uri, array $queryParams = [], array $headers = [], ?LoggerInterface $requestLogger = null): string
    {
        return $this->sendRequest('GET', $uri, [
            'query' => $queryParams,
            'headers' => $headers
        ], $requestLogger);
    }

    public function put(string $uri, array $payload, array $headers = [], ?LoggerInterface $requestLogger = null): string
    {
        return $this->sendRequest('PUT', $uri, [
            'json' => $payload,
            'headers' => $headers
        ], $requestLogger);
    }

    public function patch(string $uri, array $payload, array $headers = [], ?LoggerInterface $requestLogger = null): string
    {
        return $this->sendRequest('PATCH', $uri, [
            'json' => $payload,
            'headers' => $headers
        ], $requestLogger);
    }
}