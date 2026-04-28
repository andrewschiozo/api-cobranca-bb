<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\ValorTituloVO;
use DateTimeImmutable;

/**
 * Serviço responsável por formatar e validar os dados internos de uma Alteração
 * de Cobrança no payload JSON exigido pela API do Banco do Brasil.
 */
class AlterarBoletoFormatter
{
    /**
     * Transforma os dados da Alteração de uma cobrança em um array compatível com o payload da API.
     * @param AlterarBoletoDTO $dto
     * @return array Payload pronto para ser enviado via HTTP
     */
    public function format(AlterarBoletoDTO $dto): array
    {

        $numeroConvenio = new NumeroConvenioVO($dto->numeroConvenio);

        $payload = [
            'numeroConvenio' => $numeroConvenio->numero, 
        ];

        if($dto->dataVencimento !== null) {
            $dataVencimento = new DateTimeImmutable($dto->dataVencimento);
            $payload['indicadorNovaDataVencimento'] = 'S';
            $payload['alteracaoData'] = [
                'novaDataVencimento' => $dataVencimento->format('d.m.Y')
            ];
        }

        if($dto->valorTitulo !== null) {
            $valorTitulo = new ValorTituloVO($dto->valorTitulo);
            $payload['indicadorNovoValorNominal'] = 'S';
            $payload['alteracaoValor'] = [
                "novoValorNominal" => $valorTitulo->formatadoParaBB()
            ];
        }

        return $payload;
    }
}