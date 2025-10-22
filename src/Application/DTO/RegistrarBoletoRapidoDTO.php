<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\DTO;

use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\DocumentoVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\PagadorVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\ValorTituloVO;
use AndrewsChiozo\ApiCobrancaBb\Ports\DTOInterface;
use DateTimeImmutable;
use InvalidArgumentException;

class RegistrarBoletoRapidoDTO implements DTOInterface
{
    public function __construct(
        public readonly NumeroConvenioVO $numeroConvenio,
        public readonly DateTimeImmutable $dataVencimento,
        public readonly ValorTituloVO $valorTitulo,
        public readonly NossoNumeroVO $nossoNumero,
        public readonly PagadorVO $pagador
    ) {}

    public static function fromArray(array $data): RegistrarBoletoRapidoDTO
    {
        try{            
            self::validarInput($data);

            $numeroConvenio = new NumeroConvenioVO($data['numeroConvenio']);
            $dataVencimento = new DateTimeImmutable($data['dataVencimento']);
            $valorTitulo = new ValorTituloVO($data['valorTitulo']);
            $nossoNumero = new NossoNumeroVO($data['nossoNumero']);
            $pagador = new PagadorVO(
                documento: new DocumentoVO($data['pagador']['numeroDocumento']),
                nome: $data['pagador']['nome'] ?? null,
                endereco: $data['pagador']['endereco'] ?? null,
                cep: $data['pagador']['cep'],
                cidade: $data['pagador']['cidade'] ?? null,
                bairro: $data['pagador']['bairro'] ?? null,
                uf: $data['pagador']['uf'] ?? null,
                telefone: $data['pagador']['telefone'] ?? null,
                email: $data['pagador']['email'] ?? null,
                
            );

            return new self(
                numeroConvenio: $numeroConvenio,
                dataVencimento: $dataVencimento,
                valorTitulo: $valorTitulo,
                nossoNumero: $nossoNumero,
                pagador: $pagador,
            );
        } catch(\Exception $e){
            throw $e;
        }
    }

    private static function validarInput(array $data): void
    {
        $indicesObrigatorios = ['numeroConvenio', 'dataVencimento', 'valorTitulo', 'nossoNumero', 'pagador.numeroDocumento', 'pagador.cep'];
    
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
    }

}