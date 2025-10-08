<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBb\Domain\Services\CobrancaResponseParser;
use PHPUnit\Framework\TestCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\CobrancaFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\CobrancaManager;
use AndrewsChiozo\ApiCobrancaBb\Adapters\FakeHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;

class CobrancaManagerTest extends TestCase
{
    /**
     * Dados de cobrança mockados
     * @var array
     */
    private array $mockDadosCobranca = [
        'valor' => 100.5,
        'vencimento_data' => '2025-12-20',
        'pagador_documento' => '12345678901',
        'pagador_nome' => 'João da Silva',
        'convenio_id' => 98765,
        'nosso_numero' => 'ABC123XYZ'
    ];

    /**
     * Testa o cenário de sucesso ao emitir uma cobrança.
     */
    public function testEmissaoCobrancaComSucesso(): void
    {
        $mockDadosCobranca = $this->mockDadosCobranca;

        $respostaAPI = '{"numero_cobranca": "1234567890", "status": "REGISTRADA"}';
        
        $fakeAdapter = new FakeHttpClientAdapter();
        $fakeAdapter->setPostResponse($respostaAPI);

        $manager = new CobrancaManager($fakeAdapter, new CobrancaFormatter(), new CobrancaResponseParser());

        $resultado = $manager->emitirCobranca($mockDadosCobranca);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('numero_cobranca', $resultado);
        $this->assertArrayHasKey('status', $resultado);
        $this->assertEquals('1234567890', $resultado['numero_cobranca']);
        $this->assertEquals('REGISTRADA', $resultado['status']);
    }
    
    /**
     * Testa o cenário onde o cliente HTTP falha (simulando um timeout de rede).
     */
    public function testEmissaoCobrancaLancaExcecaoEmCasoDeFalhaHTTP(): void
    {
        $this->expectException(HttpCommunicationException::class);

        $mockAdapter = $this->createMock(FakeHttpClientAdapter::class);

        $mockAdapter
            ->method('post')
            ->willThrowException(new HttpCommunicationException('Erro de conexão simulado.'));

        $mockDadosCobranca = $this->mockDadosCobranca;
        $manager = new CobrancaManager($mockAdapter, new CobrancaFormatter(), new CobrancaResponseParser());
        $manager->emitirCobranca($mockDadosCobranca);
    }
}