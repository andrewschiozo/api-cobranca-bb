<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\ValorTituloVO;
use AndrewsChiozo\ApiCobrancaBb\Ports\DTOInterface;
use DateTimeImmutable;
use InvalidArgumentException;

class AlterarBoletoDTO implements DTOInterface
{
    public function __construct(
        public readonly NumeroConvenioVO $numeroConvenio,
        public readonly IdentificadorBoleto $numeroBoleto,
        public readonly ?DateTimeImmutable $novaDataVencimento,
        public readonly ?ValorTituloVO $novoValorTitulo,
    ) {}

    /**
     * 
     * @param array $data
     * [
     *      'numeroConvenio': '1234567',
     *      'nossoNumero': '1234567890',
     *      'novaDataVencimento: '2026-12-03',
     *      'novoValorTitulo: 123.45
     * ]
     * @return AlterarBoletoDTO
     */
    public static function fromArray(array $data): AlterarBoletoDTO
    {
        try{            
            self::validarInput($data);

            $numeroConvenio = new NumeroConvenioVO($data['numeroConvenio']);
            $nossoNumero = new NossoNumeroVO($data['nossoNumero']);
            $numeroBoleto = new IdentificadorBoleto($numeroConvenio, $nossoNumero);

            $novaDataVencimento = isset($data['novaDataVencimento']) ? new DateTimeImmutable($data['novaDataVencimento']) : null;
            $novoValorTitulo = isset($data['novoValorTitulo']) ? new ValorTituloVO($data['novoValorTitulo']) : null;
            

            return new self(
                numeroConvenio: $numeroConvenio,
                numeroBoleto: $numeroBoleto,
                novaDataVencimento: $novaDataVencimento,
                novoValorTitulo: $novoValorTitulo,
            );
        } catch(\Exception $e){
            throw $e;
        }
    }

    private static function validarInput(array $data): void
    {
        $indicesObrigatorios = ['numeroConvenio', 'nossoNumero'];
        $qtdCamposMinimos = 3;
        foreach($indicesObrigatorios as $indice){
            if(str_contains($indice, '.')) {
                $indice = explode('.', $indice);
                if(!isset($data[$indice[0]]) || !isset($data[$indice[0]][$indice[1]])) {
                    throw new InvalidArgumentException("O campo {$indice[0]}[{$indice[1]}] é obrigatório.");
                }
                continue;
            }

            if(!isset($data[$indice])){
                throw new InvalidArgumentException("O campo {$indice} é obrigatório.");
            }
        }

        if(count($data) < $qtdCamposMinimos) {
            throw new InvalidArgumentException("Nenhuma alteração informada.");
        }
    }

}