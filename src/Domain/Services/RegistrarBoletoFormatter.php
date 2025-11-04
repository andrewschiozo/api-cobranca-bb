<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoRapidoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBb\Ports\DTOInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface;

/**
 * Serviço responsável por formatar e validar os dados internos de uma Cobrança
 * no payload JSON exigido pela API do Banco do Brasil.
 */
class RegistrarBoletoFormatter implements FormatterInterface
{
    /**
     * Transforma os dados da Cobrança em um array compatível com o payload da API.
     * * @param array $cobrancaData Dados internos (ex: ['valor' => 100.50, 'cliente' => '...'])
     * @return array Payload pronto para ser enviado via HTTP
     */
    public function format(DTOInterface $dto): array
    {
        if( !$dto instanceof RegistrarBoletoRapidoDTO ) {
            throw new \InvalidArgumentException('Tipo de dado inválido. Esperado: ' . RegistrarBoletoRapidoDTO::class);
        }

        $numeroTituloCliente = IdentificadorBoleto::create($dto->numeroConvenio, $dto->nossoNumero)->identificadorCompleto;

        return [
            'numeroConvenio' => $dto->numeroConvenio->numero, 
            'dataVencimento' => $dto->dataVencimento->format('d.m.Y'),
            'valorOriginal' => $dto->valorTitulo->valor,
            'numeroTituloCliente' => $numeroTituloCliente,
            'pagador' => [
                'tipoInscricao' => $dto->pagador->documento->tipo->value,
                'numeroInscricao' => $dto->pagador->documento->valor,
                'cep' => $dto->pagador->cep
            ]
        ];
    }
}