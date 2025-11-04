<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Integration;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoRapidoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\RegistrarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\RegistrarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\RegistrarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\MockHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\LoggerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class RegistrarBoletoRapidoTest extends TestCase
{
    private string $registrarBoletoRapidoFilePath = __DIR__ . '/../Mocks/registrar-boleto/request_registrar-boleto-rapido_success.json';
    // private array $registrarBoletoFilePath = __DIR__ . '/../Mocks/registrar-boleto/request_registrar-boleto_success.json';
    private string $registrarBoletoResponseFilePath = __DIR__ . '/../Mocks/registrar-boleto/response_registrar-boleto_success.json';

    /**
     * Testa o cenário de sucesso ao emitir uma cobrança com FakeHttpClient.
     */
    public function testRegistrarBoletoRapidoComSucesso(): void
    {
        // Mock real
        $mockResponseFilePath = $this->registrarBoletoResponseFilePath;
        $uri = '/cobrancas/v2/boletos';

        // Mock Adapter
        $mockAdapter = new MockHttpClientAdapter();
        $mockAdapter->addMockResponse('POST', $uri, $mockResponseFilePath);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->method('info');
        $mockFactory = $this->createMock(LoggerFactory::class);
        $mockFactory->method('createLogger')->willReturn($mockLogger);

        // CobrancaManager
        $useCase = new RegistrarBoletoUseCase(
        $mockAdapter,
        new RegistrarBoletoFormatter(),
        new RegistrarBoletoResponseParser(),
        $mockFactory);
        
        // Dados de entrada
        $mockDadosCobranca = json_decode(file_get_contents($this->registrarBoletoRapidoFilePath), true);

        // Emitir cobranca
        $resultado = $useCase->execute(RegistrarBoletoRapidoDTO::fromArray($mockDadosCobranca));

        // Verificações
        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('numero', $resultado);
        $this->assertArrayHasKey('linhaDigitavel', $resultado);
        $this->assertTrue(strlen($resultado['numero']) == 20);
    }

    /**
     * Testa o cenário onde o cliente HTTP falha (simulando um timeout de rede).
     */
    public function testLancarExcecaoEmCasoDeFalhaHttp(): void
    {
        $this->expectException(HttpCommunicationException::class);

        $mockDadosCobranca = json_decode(file_get_contents($this->registrarBoletoRapidoFilePath), true);

        $mockAdapter = $this->createMock(MockHttpClientAdapter::class);
        $mockAdapter
            ->method('post')
            ->willThrowException(new HttpCommunicationException('Erro de conexão simulado.'));

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->method('info');
        $mockFactory = $this->createMock(LoggerFactory::class);
        $mockFactory->method('createLogger')->willReturn($mockLogger);

        $useCase = new RegistrarBoletoUseCase(
            $mockAdapter,
            new RegistrarBoletoFormatter(),
            new RegistrarBoletoResponseParser(),
            $mockFactory
        );

        $useCase->execute(RegistrarBoletoRapidoDTO::fromArray($mockDadosCobranca));
    }
}