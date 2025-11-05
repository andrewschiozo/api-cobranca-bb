<?php 

declare(strict_types=1);

namespace Andrewschiozo\ApiCobrancaBb\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\NullLogger;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\GuzzleHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\MockTokenStorageAdapter;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

class GuzzleHttpClientAdapterTest extends TestCase
{
    private const FAKE_OPTIONS_HTTP = [
        'baseUrl' => 'fake baseUrl',
        'authUrl' => 'fake authUrl',
        'clientId' => 'fake clientId',
        'clientSecret' => 'fake clientSecret',
        'appKey' => 'fake apiKey'
    ];

    private MockTokenStorageAdapter $tokenStorage;
    private NullLogger $logger;
    private ErrorResponseParser $errorParser;

    protected function setUp(): void
    {
        $this->tokenStorage = new MockTokenStorageAdapter();
        $this->logger = new NullLogger();
        $this->errorParser = new ErrorResponseParser();
    }

    #[Test]
    public function tokenDeveSerObtidoViaHttpQuandoCacheEstiverVazio(): void
    {
        $expectedToken = 'TOKEN_DE_TESTE_OBTIDO_VIA_HTTP';

        $oauthResponse = new Response(200, [], json_encode([
            'access_token' => $expectedToken,
            'token_type' => 'bearer',
            'expires_in' => 3600
        ]));

        $apiResponse = new Response(400, [], json_encode(['mensagem' => 'Payload inválido']));

        $mockHttpClient = $this->createMock(ClientInterface::class);
        $mockHttpClient->expects($this->exactly(2))
                       ->method('request')
                       ->willReturnOnConsecutiveCalls($oauthResponse, $apiResponse);
        
        $adapter = new GuzzleHttpClientAdapter(
            self::FAKE_OPTIONS_HTTP,
            $this->errorParser,
            $this->tokenStorage,
            $mockHttpClient
        );

        $adapter->post('/qualquer/endpoint', [], [], $this->logger);

        $this->assertEquals($expectedToken, $this->tokenStorage->getToken(), 'O token obtido via HTTP deve ser salvo no cache.');
    }

    #[Test]
    public function tokenCacheadoDeveSerReutilizadoParaEvitarRequisicaoHttp(): void
    {
        $cachedToken = 'TOKEN_JÁ_CACHEADO_VÁLIDO';   
        $this->tokenStorage->saveToken($cachedToken, 3600); 

        $apiResponse = new Response(200, [], json_encode(['status' => 'sucesso']));

        $mockHttpClient = $this->createMock(ClientInterface::class);
        $mockHttpClient->expects($this->once())
                       ->method('request')
                       ->willReturn($apiResponse);
        
        $adapter = new GuzzleHttpClientAdapter(
            self::FAKE_OPTIONS_HTTP,
            $this->errorParser,
            $this->tokenStorage,
            $mockHttpClient
        );
        
        $adapter->post('/fake/endpoint', [], [], $this->logger);

        $this->assertEquals($cachedToken, $this->tokenStorage->getToken(), 'O token deve ter sido recuperado do cache.');
    }
}