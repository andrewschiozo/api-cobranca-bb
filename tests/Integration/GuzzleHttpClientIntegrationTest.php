<?php 
declare(strict_types=1);

namespace Andrewschiozo\ApiCobrancaBb\Tests\Integration;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\ErrorResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\MockTokenStorageAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\BufferedLoggerInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\GuzzleHttpClientAdapter;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\LoggerFactory;

/**
 * Testa a comunicação real (HTTP) com a API do Banco do Brasil (Sandbox).
 * Este teste requer credenciais válidas no .env.
 */
class GuzzleHttpClientIntegrationTest extends TestCase
{
    private GuzzleHttpClientAdapter $adapter;
    private LoggerFactory $loggerFactory;

    protected function setUp(): void
    {
        $baseUrl = $_ENV['BB_COBRANCA_URL_BASE'] ?? null;
        $authUrl = $_ENV['BB_COBRANCA_URL_AUTH'] ?? null;
        $clientId = $_ENV['BB_COBRANCA_CLIENT_ID'] ?? null;
        $clientSecret = $_ENV['BB_COBRANCA_CLIENT_SECRET'] ?? null;
        $apiKey = $_ENV['BB_COBRANCA_APP_KEY'] ?? null;

        if (!$baseUrl || !$clientId || !$clientSecret) {
            $this->markTestSkipped('Credenciais de integração (BB_API_BASE_URL_SANDBOX, BB_CLIENT_ID, BB_CLIENT_SECRET) não configuradas no .env. Ignorando teste de integração.');
        }

        $errorParser = new ErrorResponseParser();
        $mockTokenStorage = new MockTokenStorageAdapter();
        $this->loggerFactory = new LoggerFactory(__DIR__ . '/../logs'); 

        $this->adapter = new GuzzleHttpClientAdapter([
            'baseUrl' => $baseUrl,
            'authUrl' => $authUrl,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'appKey' => $apiKey],
            $errorParser,
            $mockTokenStorage,
        );
    }
    
    #[Test]
    public function tokenDeAcessoDeveSerObtidoComSucessoELogadoEmArquivoExclusivo(): void
    {
        $testLogger = $this->loggerFactory->createLogger('GuzzleHttpClientIntegrationTest_TokenTest');

        try {
            $nossoNumeroAleatorio = date('ymd') . str_pad("" .rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $nossoNumeroAleatorio = '0003128557' . $nossoNumeroAleatorio;
            $responseJson = $this->adapter->post(
            '/cobrancas/v2/boletos',
            [
                    "numeroConvenio" => "3128557",
                    "dataVencimento" => "06.112025",
                    "valorOriginal" => "55.33",
                    "numeroTituloCliente" => $nossoNumeroAleatorio,
                    "pagador" => [
                        "tipoInscricao" => "2",
                        "numeroInscricao" => "81676009000119",
                        "cep" => "1000000"
                    ]
                ],
            [],
            $testLogger
            );
            $this->fail('A requisição deveria ter falhado');
        } catch (BBApiException $e) {
            
        }
        
        if ($testLogger instanceof BufferedLoggerInterface) {
            $testLogger->flush();
        } else {
           throw new Exception('O logger do teste não suporta o método flush()'); 
        } 

        //Se o arquivo de log foi criado
        $logPath = $this->getLastLogFilePath($this->loggerFactory);
        error_log($logPath);
        $this->assertFileExists($logPath, 'O arquivo de log exclusivo da integração deve ter sido criado.');

        //Se o token foi obtido (olha no arquivo de log se tem a string que o adapter registra no log)
        $logContent = file_get_contents($logPath);
        $this->assertStringContainsString('Token obtido com sucesso', $logContent, 'O log deve registrar a obtenção do token OAuth2.');
        
        //Apaga o arquivo de log
        $this->deleteLogFile($logPath);
    }

    private function getLastLogFilePath(LoggerFactory $factory): string
    {
        $files = glob($factory->logDir . '/*');
        if (empty($files)) {
            return '';
        }
        usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
        return $files[0];
    }

    private function deleteLogFile(string $logFile): void
    {
        error_log('arquivo para deletar: ' . $logFile);
        if(file_exists($logFile)) {
            unlink($logFile);
        }
    }
}