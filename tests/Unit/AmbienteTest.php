<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Este é apenas um teste de verificação de ambiente.
 */
class AmbienteTest extends TestCase
{
    /**
     * Um teste básico que sempre passa.
     */
    public function testEnvironmentIsWorking(): void
    {
        $this->assertTrue(true, "O ambiente de testes está configurado corretamente.");
    }

    /**
     * Testa se as credenciais e urls do Banco do Brasil foram carregadas pelo .env.
     */
    public function testCredenciaisDeAmbienteEstaoCarregadas(): void
    {
        $appKey = $_ENV['BB_COBRANCA_APP_KEY'] ?? null;
        $clientId = $_ENV['BB_COBRANCA_CLIENT_ID'] ?? null;
        $secret = $_ENV['BB_COBRANCA_CLIENT_SECRET'] ?? null;
        $urlBase = $_ENV['BB_COBRANCA_URL_BASE'] ?? null;
        $urlAuth = $_ENV['BB_COBRANCA_URL_AUTH'] ?? null;

        $this->assertIsString($appKey, 'BB_COBRANCA_APP_KEY deve ser uma string');
        $this->assertIsString($clientId, 'BB_COBRANCA_CLIENT_ID deve ser uma string');
        $this->assertIsString($secret, 'BB_COBRANCA_CLIENT_SECRET deve ser uma string');
        $this->assertIsString($urlBase, 'BB_COBRANCA_URL_BASE deve ser uma string');
        $this->assertIsString($urlAuth, 'BB_COBRANCA_URL_AUTH deve ser uma string');

        $this->assertGreaterThan(0, strlen($appKey), 'BB_COBRANCA_APP_KEY não deve estar vazio');
        $this->assertGreaterThan(0, strlen($clientId), 'BB_COBRANCA_CLIENT_ID não deve estar vazio');
        $this->assertGreaterThan(0, strlen($secret), 'BB_COBRANCA_CLIENT_SECRET não deve estar vazio');
        $this->assertGreaterThan(0, strlen($urlBase), 'BB_COBRANCA_URL_BASE não deve estar vazio');
        $this->assertGreaterThan(0, strlen($urlAuth), 'BB_COBRANCA_URL_AUTH não deve estar vazio');
    }
}