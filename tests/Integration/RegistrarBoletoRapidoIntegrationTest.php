<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\RegistrarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\RegistrarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\RegistrarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\GuzzleHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\MockTokenStorageAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\JsonFileLogger;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\NullLogger;
use PHPUnit\Framework\TestCase;

class RegistrarBoletoRapidoIntegrationTest extends TestCase
{
    private static string $nossoNumeroAleatorio;

    /**
     * Testa o cenário de sucesso ao emitir uma cobrança com GuzzleHttpClient.
     */
    public function testSandboxComSucesso(): void
    {
        $httpAdapter = new GuzzleHttpClientAdapter([
            'baseUrl' => $_ENV['BB_COBRANCA_URL_BASE'],
            'authUrl' => $_ENV['BB_COBRANCA_URL_AUTH'],
            'clientId' => $_ENV['BB_COBRANCA_CLIENT_ID'],
            'clientSecret' => $_ENV['BB_COBRANCA_CLIENT_SECRET'],
            'appKey' => $_ENV['BB_COBRANCA_APP_KEY']
        ],
        new ErrorResponseParser(),
        new MockTokenStorageAdapter(),
        new NullLogger()
    );

        $useCase = new RegistrarBoletoUseCase(
            $httpAdapter,
            new RegistrarBoletoFormatter(),
            new RegistrarBoletoResponseParser(),
            new NullLogger()
        );

        // Dados de entrada
        self::$nossoNumeroAleatorio = date('ymd') . str_pad("" .rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $dadosCobranca = [
            "numeroConvenio" => "3128557",
            "dataVencimento" => "2026-05-06",
            "valorTitulo" => "55.33",
            "nossoNumero" => self::$nossoNumeroAleatorio,
            "pagadorNumeroDocumento" => "81676009000119",
            "pagadorCep" => "1000000"
        ];

        // Emitir cobranca
        $response = $useCase->execute(RegistrarBoletoDTO::fromArray($dadosCobranca));
        // Verificações
        $this->assertIsArray($response);
        $this->assertArrayHasKey('numero', $response);
        $this->assertArrayHasKey('linhaDigitavel', $response);
        $this->assertTrue(strlen($response['numero']) == 20);

        //Regra do BB 000 + numero convenio + nosso numero (10 dígitos, com zeros a esquerda)
        $expectedNossoNumero = '000' . $dadosCobranca['numeroConvenio'] . str_pad($dadosCobranca['nossoNumero'], 10, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedNossoNumero, $response['numero']);
    }

    /**
     * Testa o cenário em que o nosso número já foi registrado.
     */
    public function testSandboxLancarExcecaoEmCasoDeNossoNumeroRepetido(): void
    {
        $this->expectException(BBApiException::class);
        $this->expectExceptionMessage('Nosso Número já incluído anteriormente.');

        $httpAdapter = new GuzzleHttpClientAdapter([
            'baseUrl' => $_ENV['BB_COBRANCA_URL_BASE'],
            'authUrl' => $_ENV['BB_COBRANCA_URL_AUTH'],
            'clientId' => $_ENV['BB_COBRANCA_CLIENT_ID'],
            'clientSecret' => $_ENV['BB_COBRANCA_CLIENT_SECRET'],
            'appKey' => $_ENV['BB_COBRANCA_APP_KEY']
        ],
        new ErrorResponseParser(),
        new MockTokenStorageAdapter(),
        new NullLogger()
    );

        $dadosCobranca = [
            "numeroConvenio" => "3128557",
            "dataVencimento" => "2026-05-06",
            "valorTitulo" => "55.33",
            "nossoNumero" => self::$nossoNumeroAleatorio,
            "pagadorNumeroDocumento" => "81676009000119",
            "pagadorCep" => "1000000"
        ];

        $useCase = new RegistrarBoletoUseCase(
            $httpAdapter,
            new RegistrarBoletoFormatter(),
            new RegistrarBoletoResponseParser(),
            new NullLogger()
        );

        $useCase->execute(RegistrarBoletoDTO::fromArray($dadosCobranca));
    }

    /**
     * Testa o cenário onde o cliente HTTP falha (simulando uma url inexistente).
     */
    public function testSandboxLancarExcecaoEmCasoDeFalhaHTTP(): void
    {
        $this->expectException(HttpCommunicationException::class);

        $httpAdapter = new GuzzleHttpClientAdapter([
            'baseUrl' => $_ENV['BB_COBRANCA_URL_BASE'],
            'authUrl' => $_ENV['BB_COBRANCA_URL_AUTH'] . '/v3',
            'clientId' => $_ENV['BB_COBRANCA_CLIENT_ID'],
            'clientSecret' => $_ENV['BB_COBRANCA_CLIENT_SECRET'],
            'appKey' => $_ENV['BB_COBRANCA_APP_KEY']
        ],
        new ErrorResponseParser(),
        new MockTokenStorageAdapter(),
        new NullLogger()
    );

        $useCase = new RegistrarBoletoUseCase(
            $httpAdapter,
            new RegistrarBoletoFormatter(),
            new RegistrarBoletoResponseParser(),
            new NullLogger()
        );

        $nossoNumeroAleatorio = date('ymd') . str_pad("" .rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $dadosCobranca = [
            "numeroConvenio" => "3128557",
            "dataVencimento" => "2026-05-06",
            "valorTitulo" => "55.33",
            "nossoNumero" => $nossoNumeroAleatorio,
            "pagadorNumeroDocumento" => "81676009000119",
            "pagadorCep" => "1000000"
        ];

        $useCase->execute(RegistrarBoletoDTO::fromArray($dadosCobranca));
    }
}