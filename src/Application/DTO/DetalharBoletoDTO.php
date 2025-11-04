<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBb\Ports\DTOInterface;
use InvalidArgumentException;

class DetalharBoletoDTO implements DTOInterface
{
    public function __construct(
        public readonly NumeroConvenioVO $numeroConvenio,
        public readonly NossoNumeroVO $nossoNumero
    ) {}

    public static function fromArray(array $data): DetalharBoletoDTO
    {
        try{            
            self::validarInput($data);

            $numeroConvenio = new NumeroConvenioVO($data['numeroConvenio']);
            $nossoNumero = new NossoNumeroVO($data['nossoNumero']);

            return new self(
                numeroConvenio: $numeroConvenio,
                nossoNumero: $nossoNumero,
            );
        } catch(\Exception $e){
            throw $e;
        }
    }

    private static function validarInput(array $data): void
    {
        $indicesObrigatorios = ['numeroConvenio', 'nossoNumero'];
    
        foreach($indicesObrigatorios as $indice){
            if(!isset($data[$indice])){
                throw new InvalidArgumentException("O campo {$indice} é obrigatório.");
            }
        }
    }

}