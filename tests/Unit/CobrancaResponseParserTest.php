<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use PHPUnit\Framework\TestCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\CobrancaResponseParser;

class CobrancaResponseParserTest extends TestCase
{
    /**
     * Testa se o parser transforma o JSON da API no formato interno esperado.
     */
    public function testParseSucesso(): void
    {
        $parser = new CobrancaResponseParser();
        
        $jsonSucesso = '{"numero_cobranca": "999888777", "status": "REGISTRADA", "data_emissao": "2025-10-08"}';
        
        $dadosInternos = $parser->parse($jsonSucesso);

        $this->assertIsArray($dadosInternos);
        $this->assertArrayHasKey('numero_cobranca', $dadosInternos);
        $this->assertEquals('999888777', $dadosInternos['numero_cobranca']);
        $this->assertEquals('REGISTRADA', $dadosInternos['status']);

        $this->assertArrayNotHasKey('data_emissao', $dadosInternos, "O campo data_emissao deve ser mapeado ou ignorado, não exposto diretamente.");
    }
    
    /**
     * Testa se o parser lança exceção ao receber um JSON inválido.
     */
    public function testParseInvalidoLancaExcecao(): void
    {
        $this->expectException(\JsonException::class);
        
        $parser = new CobrancaResponseParser();

        $jsonInvalido = '{"numero_cobranca": "999888777", '; 
        
        $parser->parse($jsonInvalido);
    }
}