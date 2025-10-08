<?php 
declare(strict_types=1);

namespace Andrewschiozo\ApiCobrancaBb\Domain\Services;

/**
 * Serviço responsável por formatar e validar os dados internos de uma Cobrança
 * no payload JSON exigido pela API do Banco do Brasil.
 */
class CobrancaFormatter
{
    /**
     * Transforma os dados da Cobrança em um array compatível com o payload da API.
     * * @param array $cobrancaData Dados internos (ex: ['valor' => 100.50, 'cliente' => '...'])
     * @return array Payload pronto para ser enviado via HTTP
     */
    public function format(array $cobrancaData): array
    {
        return [
            'numeroConvenio' => $cobrancaData['convenio_id'] ?? 1234567, 
            'numeroTituloCedente' => $cobrancaData['nosso_numero'] ?? uniqid(),
            'valorNominalTitulo' => number_format($cobrancaData['valor'], 2, '.', ''),
            'dataVencimento' => $cobrancaData['vencimento_data'],
            'indicadorAceiteTituloVencido' => 'S',
            'dadosPagador' => [
                'cpfCnpj' => $cobrancaData['pagador_documento'],
                'nome' => $cobrancaData['pagador_nome'],
            ],
        ];
    }
}