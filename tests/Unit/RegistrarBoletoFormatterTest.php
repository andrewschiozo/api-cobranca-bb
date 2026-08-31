<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\RegistrarBoletoDTO;
use PHPUnit\Framework\TestCase;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\RegistrarBoletoFormatter;

class RegistrarBoletoFormatterTest extends TestCase
{

    /**
     * Testa se o formatador transforma corretamente os dados internos para o payload do BB.
     */
    public function testFormatoBasicoCorreto(): void
    {
        $mockFilePath = __DIR__ . '/../Mocks/registrar-boleto/request_registrar-boleto-rapido_success.json';
        $mockDadosCobranca = json_decode(file_get_contents($mockFilePath), true);

        $formatter = new RegistrarBoletoFormatter();
        $payload = $formatter->format(RegistrarBoletoDTO::fromArray($mockDadosCobranca));
        
        $this->assertIsArray($payload);

        // Verifica se devolveu o nosso número igual ao nosso número enviado
        $this->assertArrayHasKey('pagador', $payload);
        $this->assertEquals($mockDadosCobranca['pagadorNumeroDocumento'], $payload['pagador']['numeroInscricao']);

        //Regra do BB 000 + numero convenio + nosso numero (10 dígitos, com zeros a esquerda)
        $expectedNossoNumero = '000' . $mockDadosCobranca['numeroConvenio'] . str_pad($mockDadosCobranca['nossoNumero'], 10, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedNossoNumero, $payload['numeroTituloCliente']);        
    }
}