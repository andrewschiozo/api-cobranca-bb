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

    /**
     * 
     * @param array $data
     * [
     *  
     * ]
     * @return RegistrarBoletoRapidoDTO
     */
    public static function fromArray(array $data): RegistrarBoletoRapidoDTO
    {
        try{            
            self::validarInput($data);

            $numeroConvenio = new NumeroConvenioVO($data['numeroConvenio']);
            $dataVencimento = new DateTimeImmutable($data['dataVencimento']);
            $valorTitulo = new ValorTituloVO($data['valorTitulo']);
            $nossoNumero = new NossoNumeroVO($data['nossoNumero']);
            $pagador = new PagadorVO(
                documento: new DocumentoVO($data['pagadorNumeroDocumento']),
                nome: $data['pagadorNome'] ?? null,
                endereco: $data['pagadorEndereco'] ?? null,
                cep: $data['pagadorCep'],
                cidade: $data['pagadorCidade'] ?? null,
                bairro: $data['pagadorBairro'] ?? null,
                uf: $data['pagadorUf'] ?? null,
                telefone: $data['pagadorTelefone'] ?? null,
                email: $data['pagadorEmail'] ?? null,
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
        $indicesObrigatorios = ['numeroConvenio', 'dataVencimento', 'valorTitulo', 'nossoNumero', 'pagadorNumeroDocumento', 'pagadorCep'];
    
        foreach($indicesObrigatorios as $indice){
            if(!isset($data[$indice])){
                throw new InvalidArgumentException("O campo {$indice} é obrigatório.");
            }
        }
    }

}