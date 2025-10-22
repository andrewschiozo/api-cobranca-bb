<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use PHPUnit\Framework\TestCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\RegistrarBoletoResponseParser;

class RegistrarBoletoResponseParserTest extends TestCase
{
    /**
     * Testa se o parser transforma o JSON da API no formato interno esperado.
     */
    public function testParseSucesso(): void
    {
        $parser = new RegistrarBoletoResponseParser();
        
        $mockFilePath = __DIR__ . '/../Mocks/registrar-boleto/response_registrar-boleto_success.json';
        $jsonSucesso = file_get_contents($mockFilePath);
        
        
        $dadosInternos = $parser->parse($jsonSucesso);

        $this->assertIsArray($dadosInternos);
        $this->assertArrayHasKey('numero', $dadosInternos);
        $this->assertEquals('00031285570001129292', $dadosInternos['numero']);

        $this->assertArrayHasKey('linhaDigitavel', $dadosInternos);
        $this->assertEquals('00190000090312855700001129292171814380000010000', $dadosInternos['linhaDigitavel']);
    }
    
    /**
     * Testa se o parser lança exceção ao receber um JSON inválido.
     */
    public function testParseInvalidoLancaExcecao(): void
    {
        $this->expectException(\JsonException::class);
        
        $parser = new RegistrarBoletoResponseParser();

        $jsonInvalido = '{"numero_cobranca": "999888777", '; 
        
        $parser->parse($jsonInvalido);
    }
}