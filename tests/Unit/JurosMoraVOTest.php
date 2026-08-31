<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBB\Domain\Enums\JurosMoraTipoEnum;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\JurosMoraInvalidoException;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\JurosMoraVO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class JurosMoraVOTest extends TestCase
{
    #[DataProvider('provedorValoresValidos')]
    public function testDeveInstanciarJurosMoraComSucesso(
        JurosMoraTipoEnum $tipo,
        string $valorEntrada,
        string $valorEsperadoString
    ): void {
        $vo = new JurosMoraVO($tipo, $valorEntrada);

        $this->assertSame($tipo, $vo->tipo);
        $this->assertSame($valorEsperadoString, (string) $vo);
    }

    #[DataProvider('provedorFormatosInvalidos')]
    public function testLancaExcecaoQuandoFormatoDoValorForInvalido(string $valorInvalido): void
    {
        $this->expectException(JurosMoraInvalidoException::class);
        $this->expectExceptionMessage("O valor do juros de mora não é um formato monetário ou percentual string válido.");

        new JurosMoraVO(JurosMoraTipoEnum::VALOR_DIA_ATRASO, $valorInvalido);
    }

    public function testLancaExcecaoQuandoValorForNegativo(): void
    {
        $this->expectException(JurosMoraInvalidoException::class);
        $this->expectExceptionMessage("O valor do juros de mora não pode ser negativo.");

        new JurosMoraVO(JurosMoraTipoEnum::VALOR_DIA_ATRASO, "-1.50");
    }

    public function testLancaExcecaoQuandoTaxaMensalExcederCemPorCento(): void
    {
        $this->expectException(JurosMoraInvalidoException::class);
        $this->expectExceptionMessage("O valor do juros de mora percentual não pode exceder o limite de 100%.");

        new JurosMoraVO(JurosMoraTipoEnum::TAXA_MENSAL, "100.01");
    }

    public static function provedorValoresValidos(): array
    {
        return [
            'valor diario com centavos' => [
                JurosMoraTipoEnum::VALOR_DIA_ATRASO,
                '2.50',
                '2.50'
            ],
            'valor diario inteiro' => [
                JurosMoraTipoEnum::VALOR_DIA_ATRASO,
                '5',
                '5.00'
            ],
            'taxa mensal com espacos' => [
                JurosMoraTipoEnum::TAXA_MENSAL,
                '  1.50  ',
                '1.50'
            ],
            'taxa mensal limite maximo (100%)' => [
                JurosMoraTipoEnum::TAXA_MENSAL,
                '100.00',
                '100.00'
            ],
            'juros zero' => [
                JurosMoraTipoEnum::VALOR_DIA_ATRASO,
                '0.00',
                '0.00'
            ],
        ];
    }

    public static function provedorFormatosInvalidos(): array
    {
        return [
            'texto invalido' => ['abc'],
            'separador com virgula' => ['1,50'],
            'mais de duas casas decimais' => ['1.555'],
            'string vazia' => [''],
            'somente espacos' => ['   '],
            'simbolo de moeda' => ['R$ 2.00'],
        ];
    }
}