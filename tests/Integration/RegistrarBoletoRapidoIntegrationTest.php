<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\AutenticarDTO;
use AndrewsChiozo\ApiCobrancaBB\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBB\Application\UseCases\AutenticarUseCase;
use AndrewsChiozo\ApiCobrancaBB\Application\UseCases\RegistrarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\RegistrarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\RegistrarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\AutenticarFormatter;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\AutenticarResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Infrastructure\Adapters\BBHttpClientAdapter;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class RegistrarBoletoRapidoIntegrationTest extends TestCase
{
    private static string $nossoNumeroAleatorio;

    public function makeHttpClient(): BBHttpClientAdapter
    {
        return new BBHttpClientAdapter(
            new ErrorResponseParser(),
            new Client([
                'base_uri' => $_ENV['BB_COBRANCA_URL_BASE'],
                'verify'   => false
            ])
        );
    }
    private function obterToken(): string
    {
        $useCase = new AutenticarUseCase(
            $this->makeHttpClient(),
            new AutenticarFormatter(),
            new AutenticarResponseParser()
        );

        $response = $useCase->execute(
            new AutenticarDTO(
                $_ENV['BB_COBRANCA_URL_AUTH'],
                $_ENV['BB_COBRANCA_CLIENT_ID'],
                $_ENV['BB_COBRANCA_CLIENT_SECRET'],
                $_ENV['BB_COBRANCA_SCOPE']
            )
        );

        return $response->accessToken;
    }

    /**
     * Testa o cenário de sucesso ao emitir uma cobrança com GuzzleHttpClient.
     */
    public function testSandboxComSucesso(): void
    {
        $useCase = new RegistrarBoletoUseCase(
            $this->makeHttpClient()->withAuth($this->obterToken(), $_ENV['BB_COBRANCA_APP_KEY']),
            new RegistrarBoletoFormatter(),
            new RegistrarBoletoResponseParser()
        );

        // Dados de entrada
        self::$nossoNumeroAleatorio = date('ymd') . str_pad("" .rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $dadosCobranca = [
            "numeroConvenio" => "3128557",
            "dataVencimento" => date("Y-m-d", strtotime("+30 days")),
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

        $dadosCobranca = [
            "numeroConvenio" => "3128557",
            "dataVencimento" => date("Y-m-d", strtotime("+30 days")),
            "valorTitulo" => "55.33",
            "nossoNumero" => self::$nossoNumeroAleatorio,
            "pagadorNumeroDocumento" => "81676009000119",
            "pagadorCep" => "1000000"
        ];

        $useCase = new RegistrarBoletoUseCase(
            $this->makeHttpClient(),
            new RegistrarBoletoFormatter(),
            new RegistrarBoletoResponseParser()
        );

        $useCase->execute(RegistrarBoletoDTO::fromArray($dadosCobranca));
    }

    /**
     * Testa o cenário onde o cliente HTTP falha (simulando uma url inexistente).
     */
    public function testSandboxLancarExcecaoEmCasoDeFalhaHTTP(): void
    {
        $this->expectException(BBApiException::class);

        $httpClient = new BBHttpClientAdapter(
            new ErrorResponseParser(),
            new Client([
                'base_uri' => "https://api2.hmm.bb.com.br/cobrancas/v2",
                'verify'   => false
            ])
        )->withAuth($this->obterToken(), $_ENV['BB_COBRANCA_APP_KEY']);

        $useCase = new RegistrarBoletoUseCase(
            $httpClient,
            new RegistrarBoletoFormatter(),
            new RegistrarBoletoResponseParser()
        );

        $nossoNumeroAleatorio = date('ymd') . str_pad("" .rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $dadosCobranca = [
            "numeroConvenio" => "3128557",
            "dataVencimento" => date("Y-m-d", strtotime("+30 days")),
            "valorTitulo" => "55.33",
            "nossoNumero" => $nossoNumeroAleatorio,
            "pagadorNumeroDocumento" => "81676009000119",
            "pagadorCep" => "1000000"
        ];
    
        $useCase->execute(RegistrarBoletoDTO::fromArray($dadosCobranca));        
    }
}