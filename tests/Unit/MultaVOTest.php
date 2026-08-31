<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBB\Domain\Enums\MultaTipoEnum;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\MultaInvalidoException;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\MultaVO;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MultaVOTest extends TestCase
{
    private DateTimeImmutable $dataDummy;

    protected function setUp(): void
    {
        $this->dataDummy = new DateTimeImmutable('2026-12-31');
    }

    #[DataProvider('provedorValoresValidos')]
    public function testDeveInstanciarMultaComSucesso(
        MultaTipoEnum $tipo,
        string $valorEntrada,
        string $valorEsperadoString
    ): void {
        $vo = new MultaVO($tipo, $valorEntrada, $this->dataDummy);

        $this->assertSame($tipo, $vo->tipo);
        $this->assertSame($this->dataDummy, $vo->data);
        $this->assertSame($valorEsperadoString, (string) $vo);
    }

    #[DataProvider('provedorFormatosInvalidos')]
    public function testLancaExcecaoQuandoFormatoDoValorForInvalido(string $valorInvalido): void
    {
        $this->expectException(MultaInvalidoException::class);
        $this->expectExceptionMessage("O valor da multa não é um formato monetário ou percentual string válido.");

        new MultaVO(MultaTipoEnum::VALOR_FIXO, $valorInvalido, $this->dataDummy);
    }

    public function testLancaExcecaoQuandoValorForNegativo(): void
    {
        $this->expectException(MultaInvalidoException::class);
        $this->expectExceptionMessage("O valor da multa não pode ser negativo.");

        new MultaVO(MultaTipoEnum::VALOR_FIXO, "-5.00", $this->dataDummy);
    }

    public function testLancaExcecaoQuandoPercentualExcederCemPorCento(): void
    {
        $this->expectException(MultaInvalidoException::class);
        $this->expectExceptionMessage("O valor da multa percentual não pode exceder o limite de 100%.");

        new MultaVO(MultaTipoEnum::PERCENTUAL, "100.01", $this->dataDummy);
    }

    public static function provedorValoresValidos(): array
    {
        return [
            'valor fixo com centavos' => [
                MultaTipoEnum::VALOR_FIXO,
                '10.00',
                '10.00'
            ],
            'valor fixo inteiro' => [
                MultaTipoEnum::VALOR_FIXO,
                '15',
                '15.00'
            ],
            'valor com espaços' => [
                MultaTipoEnum::VALOR_FIXO,
                '  20.50  ',
                '20.50'
            ],
            'percentual limite maximo (100%)' => [
                MultaTipoEnum::PERCENTUAL,
                '100.00',
                '100.00'
            ],
            'percentual padrao (2%)' => [
                MultaTipoEnum::PERCENTUAL,
                '2.00',
                '2.00'
            ],
            'valor zero' => [
                MultaTipoEnum::VALOR_FIXO,
                '0.00',
                '0.00'
            ],
        ];
    }

    public static function provedorFormatosInvalidos(): array
    {
        return [
            'texto com letras' => ['invalid'],
            'separador com vírgula' => ['10,00'],
            'mais de duas casas decimais' => ['2.555'],
            'string vazia' => [''],
            'somente espaços' => ['   '],
            'símbolo de moeda' => ['R$ 10.00'],
        ];
    }
}