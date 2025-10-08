<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use PHPUnit\Framework\TestCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\CobrancaManager;
use AndrewsChiozo\ApiCobrancaBb\Adapters\FakeHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;

class CobrancaManagerTest extends TestCase
{
    /**
     * Testa o cenário de sucesso ao emitir uma cobrança.
     */
    public function testEmissaoCobrancaComSucesso(): void
    {
        $dadosCobranca = [
            'valor' => 100.50,
            'vencimento' => '2025-10-30',
        ];

        $respostaAPI = '{"numero_cobranca": "1234567890", "status": "REGISTRADA"}';
        
        $fakeAdapter = new FakeHttpClientAdapter();
        $fakeAdapter->setPostResponse($respostaAPI);

        $manager = new CobrancaManager($fakeAdapter);

        $resultado = $manager->emitirCobranca($dadosCobranca);

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

        $manager = new CobrancaManager($mockAdapter);
        $manager->emitirCobranca([]);
    }
}