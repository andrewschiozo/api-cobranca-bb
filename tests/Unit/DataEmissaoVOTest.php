<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Tests\Unit;

use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\DataEmissaoVO;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class DataEmissaoVOTest extends TestCase
{
    private function criarClockMock(DateTimeImmutable $dataAtual): ClockInterface
    {
        $clock = $this->createMock(ClockInterface::class);
        $clock->method('now')->willReturn($dataAtual);

        return $clock;
    }

    public function testDeveCriarDataEmissaoComSucessoPassandoApenasAData(): void
    {
        $vo = new DataEmissaoVO(new DateTimeImmutable('today'));

        $this->assertInstanceOf(DataEmissaoVO::class, $vo);
    }

    public function testDeveValidarDataEmissaoValidaComClockMockado(): void
    {
        $hoje = new DateTimeImmutable('2026-08-07');
        $clock = $this->criarClockMock($hoje);
        $dataValida = new DateTimeImmutable('2026-08-05');

        $vo = DataEmissaoVO::withClock($dataValida, $clock);

        $this->assertSame('2026-08-05', $vo->data->format('Y-m-d'));
    }

    public function testLancaExcecaoQuandoDataForFuturaEmRelacaoAoClock(): void
    {
        $hoje = new DateTimeImmutable('2026-08-07');
        $clock = $this->criarClockMock($hoje);
        $dataFutura = new DateTimeImmutable('2026-08-08');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Data de emissão não pode ser futura.");

        DataEmissaoVO::withClock($dataFutura, $clock);
    }

    public function testLancaExcecaoQuandoDataForAnteriorAUmAnoAtrasEmRelacaoAoClock(): void
    {
        $hoje = new DateTimeImmutable('2026-08-07'); // Mínimo automático: 07/08/2025
        $clock = $this->criarClockMock($hoje);
        $dataMuitoAntiga = new DateTimeImmutable('2025-08-06');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Data de emissão não pode ser anterior a 07/08/2025.");

        DataEmissaoVO::withClock($dataMuitoAntiga, $clock);
    }

    public function testPermiteSobrescreverLimitesManualmentesSeNecessario(): void
    {
        $data = new DateTimeImmutable('2026-08-10');
        $maximaCustomizada = new DateTimeImmutable('2026-08-15');

        $vo = new DataEmissaoVO(
            data: $data,
            dataMaximaLimite: $maximaCustomizada
        );

        $this->assertSame('2026-08-10', $vo->data->format('Y-m-d'));
    }
}