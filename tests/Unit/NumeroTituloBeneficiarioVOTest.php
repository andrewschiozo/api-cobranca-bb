<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\NumeroTituloBeneficiarioInvalidoException;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NumeroTituloBeneficiarioVO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NumeroTituloBeneficiarioVOTest extends TestCase
{
    #[DataProvider('valoresValidosProvider')]
    public function testDeveInstanciarVOComNumeroTituloValido(string $numeroTituloValido): void
    {
        $vo = new NumeroTituloBeneficiarioVO($numeroTituloValido);

        $this->assertSame(mb_strtoupper($numeroTituloValido), $vo->numeroTitulo);
    }

    #[DataProvider('valoresInvalidosProvider')]
    public function testLancaExcecaoQuandoNumeroTituloForInvalido(string $numeroTituloInvalido): void
    {
        $this->expectException(NumeroTituloBeneficiarioInvalidoException::class);

        new NumeroTituloBeneficiarioVO($numeroTituloInvalido);
    }

    public static function valoresValidosProvider(): array
    {
        return [
            '1 caractere (mínimo)' => ['A'],
            '15 caracteres (máximo)' => ['123456789012345'],
            'caracteres minúsculos' => ['abcdef'],
            'apenas números' => ['123456789'],
            'apenas letras' => ['ABCDEF'],
            'alfanumérico' => ['TITULO123'],
            'com hífen' => ['TIT-123'],
            'com apóstrofo' => ["D'EL-REI"],
            'com espaço' => ['TITULO 01'],
            'exemplo doc 1' => ["D'ALCORTIVO"],
            'exemplo doc 2' => ["SANT'ANA"],
            'exemplo doc 3 com 15 chars' => ['O001-ABC-DEF-01'],
        ];
    }

    public static function valoresInvalidosProvider(): array
    {
        return [
            'string vazia' => [''],
            'excede 15 caracteres (16 chars)' => ['1234567890123456'],
            'com acentuação' => ['TÍTULO123'],
            'com ç' => ['COBRANÇA'],
            'com ponto' => ['TITULO.123'],
            'com barra' => ['TITULO/123'],
            'com arroba ou símbolos' => ['TITULO@123'],
            'com underline' => ['TITULO_123'],
        ];
    }
}