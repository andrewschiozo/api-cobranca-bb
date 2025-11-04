<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects;

/**
 * Value Object responsável por encapsular a regra de formatação do
 * identificador único do boleto exigido pela API do BB (o Nosso Número Completo).
 */
class IdentificadorBoleto
{
    public readonly string $identificadorCompleto;

    public function __construct(NumeroConvenioVO $convenio, NossoNumeroVO $nossoNumero)
    {
        $convenioFormatado = $convenio->numero;
        $nossoNumeroFormatado = str_pad($nossoNumero->nossoNumero, 10, '0', STR_PAD_LEFT);
        
        $this->identificadorCompleto = "000" . $convenioFormatado . $nossoNumeroFormatado;
    }

    public static function create(NumeroConvenioVO $convenio, NossoNumeroVO $nossoNumero): self
    {
        return new self($convenio, $nossoNumero);
    }
}