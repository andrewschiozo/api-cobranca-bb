<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;


use AndrewsChiozo\ApiCobrancaBb\Application\CobrancaManager;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\RegistrarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\RegistrarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\RegistrarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\FakeHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\MockHttpClientAdapter;
use PHPUnit\Framework\TestCase;

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

        $useCase = new RegistrarBoletoUseCase($fakeAdapter, new RegistrarBoletoFormatter(), new RegistrarBoletoResponseParser());
        $manager = new CobrancaManager($useCase);

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
        $useCase = new RegistrarBoletoUseCase($mockAdapter, new RegistrarBoletoFormatter(), new RegistrarBoletoResponseParser());
        $manager = new CobrancaManager($useCase);
        $manager->emitirCobranca($mockDadosCobranca);
    }

    /**
     * Testa o cenário de sucesso ao emitir uma cobrança com um mock real.
     */
    public function testEmissaoCobrancaComSucessoComMockReal(): void
    {
        // 1. Arrange (Preparação)

        // Define o caminho para o mock real
        $mockFilePath = __DIR__ . '/../Mocks/success_cobranca_post.json';
        $uri = '/cobrancas/v2/boletos';

        // Configura o Mock Adapter
        $mockAdapter = new MockHttpClientAdapter();
        $mockAdapter->addMockResponse('POST', $uri, $mockFilePath); // Passa o caminho do arquivo

        // Injeta as dependências no Manager
        $useCase = new RegistrarBoletoUseCase($mockAdapter, new RegistrarBoletoFormatter(), new RegistrarBoletoResponseParser());
        $manager = new CobrancaManager($useCase);

        // Dados de entrada (o formatter precisa deles)
        $dadosCobranca = [
            'valor' => 100.5,
            'vencimento_data' => '2025-12-20',
            'pagador_documento' => '12345678901',
            'pagador_nome' => 'João da Silva',
            'convenio_id' => 98765,
            'nosso_numero' => 'ABC123XYZ'
        ];

        // 2. Act (Ação)
        $resultado = $manager->emitirCobranca($dadosCobranca);

        // 3. Assert (Verificação)
        // O Parser deve ter transformado o campo 'status' do JSON real (REGISTRADA)
        $this->assertIsArray($resultado);
        // $this->assertEquals('REGISTRADA', $resultado['status']);

        // Se o Parser mapear o campo 'id' para 'numero', testamos isso:
        // $this->assertEquals(999888777, $resultado['id']); // Assumindo que o parser mapeia 'numero' para 'id'
    }
}