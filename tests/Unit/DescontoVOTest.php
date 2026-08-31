<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBB\Domain\Enums\DescontoTipoEnum;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\DescontoInvalidoException;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\DescontoVO;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DescontoVOTest extends TestCase
{
    private DateTimeImmutable $dataLimiteDummy;

    protected function setUp(): void
    {
        $this->dataLimiteDummy = new DateTimeImmutable('2026-12-31');
    }

    #[DataProvider('provedorValoresValidos')]
    public function testDeveInstanciarDescontoComSucesso(
        DescontoTipoEnum $tipo,
        string $valorEntrada,
        string $valorEsperadoString
    ): void {
        $vo = new DescontoVO($tipo, $valorEntrada, $this->dataLimiteDummy);

        $this->assertSame($tipo, $vo->tipo);
        $this->assertSame($this->dataLimiteDummy, $vo->dataLimite);
        $this->assertSame($valorEsperadoString, (string) $vo);
    }

    #[DataProvider('provedorFormatosInvalidos')]
    public function testLancaExcecaoQuandoFormatoDoValorForInvalido(string $valorInvalido): void
    {
        $this->expectException(DescontoInvalidoException::class);
        $this->expectExceptionMessage("O valor do desconto não é um formato monetário ou percentual string válido.");

        new DescontoVO(DescontoTipoEnum::VALOR_FIXO_ATE_DATA, $valorInvalido, $this->dataLimiteDummy);
    }

    public function testLancaExcecaoQuandoValorForNegativo(): void
    {
        $this->expectException(DescontoInvalidoException::class);
        $this->expectExceptionMessage("O valor do desconto não pode ser negativo.");

        new DescontoVO(DescontoTipoEnum::VALOR_FIXO_ATE_DATA, "-10.00", $this->dataLimiteDummy);
    }

    public function testLancaExcecaoQuandoPercentualExcederCemPorCento(): void
    {
        $this->expectException(DescontoInvalidoException::class);
        $this->expectExceptionMessage("O valor do desconto percentual não pode exceder o limite de 100%.");

        new DescontoVO(DescontoTipoEnum::PERCENTUAL_ATE_DATA, "100.01", $this->dataLimiteDummy);
    }

    public static function provedorValoresValidos(): array
    {
        return [
            'valor fixo com centavos' => [
                DescontoTipoEnum::VALOR_FIXO_ATE_DATA,
                '15.50',
                '15.50'
            ],
            'valor fixo inteiro' => [
                DescontoTipoEnum::VALOR_FIXO_ATE_DATA,
                '100',
                '100.00'
            ],
            'valor fixo com espaços' => [
                DescontoTipoEnum::VALOR_FIXO_ATE_DATA,
                '  50.25  ',
                '50.25'
            ],
            'percentual limite maximo (100%)' => [
                DescontoTipoEnum::PERCENTUAL_ATE_DATA,
                '100.00',
                '100.00'
            ],
            'percentual valido' => [
                DescontoTipoEnum::PERCENTUAL_ATE_DATA,
                '10.5',
                '10.50'
            ],
            'valor zero' => [
                DescontoTipoEnum::VALOR_FIXO_ATE_DATA,
                '0.00',
                '0.00'
            ],
        ];
    }

    public static function provedorFormatosInvalidos(): array
    {
        return [
            'texto com letras' => ['abc'],
            'separador com virgula' => ['10,50'],
            'mais de duas casas decimais' => ['10.555'],
            'string vazia' => [''],
            'somente espacos' => ['   '],
            'simbolo de moeda' => ['R$ 10.00'],
        ];
    }
}