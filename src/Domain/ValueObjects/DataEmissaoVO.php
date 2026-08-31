<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;
use Psr\Clock\ClockInterface;

readonly class DataEmissaoVO
{
    public DateTimeImmutable $data;

    /**
     * Construtor da classe DataEmissaoVO.
     * Valida se a data de emissão está dentro do intervalo permitido.
     * Se a data máxima não for fornecida, assume-se a data atual como limite máximo.
     * Se a data mínima não for fornecida, assume-se um ano antes da data máxima como limite mínimo.
     *
     * @param DateTimeImmutable $data
     * @param ?DateTimeImmutable $dataMinimaLimite
     * @param ?DateTimeImmutable $dataMaximaLimite
     * @param ?ClockInterface $clock
     * @throws InvalidArgumentException
     */
    public function __construct(
        DateTimeImmutable $data,
        ?DateTimeImmutable $dataMaximaLimite = null,
        ?DateTimeImmutable $dataMinimaLimite = null,
        ?ClockInterface $clock = null
    ) {
        $dataMaximaLimite = $dataMaximaLimite ?? ($clock?->now() ?? new DateTimeImmutable('today'));
        $dataMinimaLimite = $dataMinimaLimite ?? $dataMaximaLimite->modify('-1 year');

        if ($data > $dataMaximaLimite) {
            throw new InvalidArgumentException("Data de emissão não pode ser futura.");
        }

        if ($data < $dataMinimaLimite) {
            throw new InvalidArgumentException("Data de emissão não pode ser anterior a {$dataMinimaLimite->format('d/m/Y')}.");
        }

        $this->data = $data;
    }

    /**
     * Named Constructor p/ injetar um Clock(PSR-20)
     */
    public static function withClock(
        DateTimeImmutable $data,
        ClockInterface $clock,
        ?DateTimeImmutable $dataMaximaLimite = null,
        ?DateTimeImmutable $dataMinimaLimite = null
    ): self {
        return new self(
            data: $data,
            dataMaximaLimite: $dataMaximaLimite,
            dataMinimaLimite: $dataMinimaLimite,
            clock: $clock
        );
    }
}