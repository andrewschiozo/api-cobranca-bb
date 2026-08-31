<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\DinheiroInvalidoException;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\DinheiroVO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DinheiroVOTest extends TestCase
{

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('valoresValidosProvider')]
    public function testDeveConverterEFormatarCorretamente(string $entrada, string $saidaEsperada): void
    {
        $dinheiro = new DinheiroVO($entrada);

        $this->assertSame($saidaEsperada, $dinheiro->__toString());
        $this->assertSame($saidaEsperada, (string)$dinheiro);
    }

    public static function valoresValidosProvider(): array
    {
        return [
            'com duas casas decimais' => ['100.50', '100.50'],
            'sem casas decimais'      => ['100', '100.00'],
            'com apenas uma decimal'  => ['100.5', '100.50'],
            'valor zero'              => ['0.00', '0.00'],
            'valor negativo'          => ['-50.25', '-50.25'],
        ];
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('validacaoZeroOuMenorProvider')]
    public function testDeveValidarMenorOuIgualAZero(string $valor, bool $resultadoEsperado): void
    {
        $dinheiro = new DinheiroVO($valor);

        $this->assertSame($resultadoEsperado, $dinheiro->isMenorOuIgualAZero());
    }

    public static function validacaoZeroOuMenorProvider(): array
    {
        return [
            'negativo deve ser true'   => ['-0.01', true],
            'zero deve ser true'       => ['0.00', true],
            'positivo deve ser false'  => ['0.01', false],
            'grande deve ser false'    => ['1500.00', false],
        ];
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('valoresInvalidosProvider')]
    public function testDeveLancarExcecaoParaValoresInvalidos(string $valorInvalido): void
    {
        $this->expectException(DinheiroInvalidoException::class);

        new DinheiroVO($valorInvalido);
    }

    public static function valoresInvalidosProvider(): array
    {
        return [
            'string de texto pura'        => ['texto'],
            'string vazia'                => [''],
            'caracteres especiais'        => ['100$00'],
            'vírgula ao invés de ponto'   => ['100,50'],
            'múltiplos pontos decimais'   => ['100.50.20'],
            'espaço no meio do número'    => ['10 0.50'],
            'apenas espaços'              => ['   '],
            'tres casas decimais'         => ['100.559'],
            'notação científica'          => ['1e2'],
            'ponto flutuante solto'       => ['100.'],
        ];
    }
}