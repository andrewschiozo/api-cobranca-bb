<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters;

use AndrewsChiozo\ApiCobrancaBb\Domain\Services\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class GuzzleHttpClientAdapter implements HttpClientInterface
{
    private GuzzleClient $client;
    private string $baseUrl;
    private string $authUrl;
    private string $clientId;
    private string $clientSecret;
    private string $appKey;
    private ?string $accessToken = null; //adicionar cache futuramente

    private ErrorResponseParser $errorParser;


    public function __construct(array $options, ErrorResponseParser $errorParser)
    {
        $this->baseUrl = $options['baseUrl'];
        $this->authUrl = $options['authUrl'];
        $this->clientId = $options['clientId'];
        $this->clientSecret = $options['clientSecret'];
        $this->appKey = $options['appKey'];
        $this->errorParser = $errorParser;
        
        //ssl desabilitado p/ testes
        $this->client = new GuzzleClient(['base_uri' => $this->baseUrl, 'verify' => false]);
    }

    private function getAccessToken(): string
    {
        // Se o token já existe. //verificar depois se está expirado
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            //Autenticação c/ oauth
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
            $this->accessToken = $data['access_token'];
            
            return $this->accessToken;

        } catch (RequestException $e) {
            throw new HttpCommunicationException('Falha na autenticação com o Banco do Brasil: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    private function sendRequest(string $method, string $uri, array $options): string
    {
        try {
            $token = $this->getAccessToken();
            
            $options['headers']['Authorization'] = 'Bearer ' . $token;
            $options['headers']['X-Application-Key'] = $this->appKey;

            $response = $this->client->request($method, $uri, $options);

            return $response->getBody()->getContents();

        } catch (RequestException $e) {
            $httpCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : '';

            if ($responseBody) {
                $this->errorParser->parse($httpCode, $responseBody);
            }

            throw new HttpCommunicationException('Erro na requisição para a API do BB: ' . $e->getMessage(), $e->getCode(), $e);
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
}