<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Ports\DTOInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface;

/**
 * Serviço responsável por formatar e validar os dados internos de uma Alteração
 * de Cobrança no payload JSON exigido pela API do Banco do Brasil.
 */
class AlterarBoletoFormatter implements FormatterInterface
{
    /**
     * Transforma os dados da Alteração de uma cobrança em um array compatível com o payload da API.
     * @param AlterarBoletoDTO $dto
     * @return array Payload pronto para ser enviado via HTTP
     */
    public function format(DTOInterface $dto): array
    {
        if( !$dto instanceof AlterarBoletoDTO ) {
            throw new \InvalidArgumentException('Tipo de dado inválido. Esperado: ' . AlterarBoletoDTO::class);
        }
        $payload = [
            'numeroConvenio' => $dto->numeroConvenio->numero, 
        ];

        if($dto->novaDataVencimento !== null) {
            $payload['indicadorNovaDataVencimento'] = 'S';
            $payload['alteracaoData'] = [
                'novaDataVencimento' => $dto->novaDataVencimento->format('d.m.Y')
            ];
        }

        if($dto->novoValorTitulo !== null) {
            $payload['indicadorNovoValorNominal'] = 'S';
            $payload['alteracaoValor'] = [
                "novoValorNominal" => $dto->novoValorTitulo->valor
            ];
        }

        return $payload;
    }
}