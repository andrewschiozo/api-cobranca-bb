<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AutenticarDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\AutenticarUseCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\DTOs\Responses\TokenResponseDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters\AutenticarFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\AutenticarResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\BBHttpClientAdapter;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class AutenticarIntegrationTest extends TestCase
{
    private static string $nossoNumeroAleatorio;


    /**
     * Testa o cenário de sucesso ao obter o token de autenticação.
     */
    public function testSandboxComSucesso(): void
    {
    
        $httpAdapter = new BBHttpClientAdapter(
            new ErrorResponseParser(),
            new Client([
                'base_uri' => $_ENV['BB_COBRANCA_URL_BASE'],
                'verify'   => false
            ])
        );

        $useCase = new AutenticarUseCase(
            $httpAdapter,
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

        $this->assertInstanceOf(TokenResponseDTO::class, $response);
    }

    public function testSandboxLancarExcecaoEmCasoDeCredenciaisInvalidas(): void
    {
        $this->expectException(BBApiException::class);

        $httpAdapter = new BBHttpClientAdapter(
            new ErrorResponseParser(),
            new Client([
                'base_uri' => $_ENV['BB_COBRANCA_URL_BASE'],
                'verify'   => false
            ])
        );

        $useCase = new AutenticarUseCase(
            $httpAdapter,
            new AutenticarFormatter(),
            new AutenticarResponseParser()
        );

        $useCase->execute(
            new AutenticarDTO(
                $_ENV['BB_COBRANCA_URL_AUTH'],
                'client id invalido',
                'client secret invalido',
                $_ENV['BB_COBRANCA_SCOPE']
            )
        );
    }

}