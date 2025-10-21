<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use PHPUnit\Framework\TestCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\RegistrarBoletoFormatter;

class RegistrarBoletoFormatterTest extends TestCase
{
    private array $mockDadosCobranca = [
        'valor' => 100.5,
        'vencimento_data' => '2025-12-20',
        'pagador_documento' => '12345678901',
        'pagador_nome' => 'João da Silva',
        'convenio_id' => 98765,
        'nosso_numero' => 'ABC123XYZ'
    ];

    /**
     * Testa se o formatador transforma corretamente os dados internos para o payload do BB.
     */
    public function testFormatoBasicoCorreto(): void
    {

        $mockDadosCobranca = $this->mockDadosCobranca;
        $formatter = new RegistrarBoletoFormatter();
        $payload = $formatter->format($mockDadosCobranca);
        
        $this->assertIsArray($payload);
        
        // Verifica se o valor foi formatado para string com 2 casas decimais
        $this->assertArrayHasKey('valorNominalTitulo', $payload);
        $this->assertEquals('100.50', $payload['valorNominalTitulo']);
        
        // Verifica se o mapeamento de campos ocorreu
        $this->assertArrayHasKey('dataVencimento', $payload);
        $this->assertEquals($mockDadosCobranca['vencimento_data'], $payload['dataVencimento']);

        // Verifica a estrutura do pagador (aninhamento)
        $this->assertArrayHasKey('dadosPagador', $payload);
        $this->assertEquals($mockDadosCobranca['pagador_documento'], $payload['dadosPagador']['cpfCnpj']);
    }
}