<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\DocumentoVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\PagadorVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\ValorTituloVO;
use DateTimeImmutable;

/**
 * Serviço responsável por formatar e validar os dados internos de uma Cobrança
 * no payload JSON exigido pela API do Banco do Brasil.
 */
class RegistrarBoletoFormatter
{
    private array $data;
    /**
     * Transforma os dados da Cobrança em um array compatível com o payload da API.
     * * @param array $cobrancaData Dados internos (ex: ['valor' => 100.50, 'cliente' => '...'])
     * @return array Payload pronto para ser enviado via HTTP
     */
    public function format(RegistrarBoletoDTO $dto): array
    {
        $convenio = new NumeroConvenioVO($dto->numeroConvenio);
        $nossoNumero = new NossoNumeroVO($dto->nossoNumero);
        $dataVencimento = new DateTimeImmutable($dto->dataVencimento);
        $valortitulo = new ValorTituloVO($dto->valorTitulo);
        $pagador = new PagadorVO(
            documento: new DocumentoVO($dto->pagadorNumeroDocumento),
            nome: $dto->pagadorNome ?? null,
            endereco: $dto->pagadorEndereco ?? null,
            cep: $dto->pagadorCep,
            cidade: $dto->pagadorCidade ?? null,
            bairro: $dto->pagadorBairro ?? null,
            uf: $dto->pagadorUf ?? 'SP',
            telefone: $dto->pagadorTelefone ?? null,
            email: $dto->pagadorEmail ?? null,
        );

        $numeroTituloCliente = IdentificadorBoleto::create($convenio, $nossoNumero)->identificadorCompleto;


        $this->data = [
            'numeroConvenio' => $convenio->numero, 
            'dataVencimento' => $dataVencimento->format('d.m.Y'),
            'valorOriginal' => $valortitulo->formatadoParaBB(),
            'numeroTituloCliente' => $numeroTituloCliente,
            'pagador' => [
                'tipoInscricao' => $pagador->documento->tipo->value,
                'numeroInscricao' => $pagador->documento->valor,
                'cep' => $pagador->cep
            ],
        ];

        $this->addDataEmissao($dto->dataEmissao);

        return $this->data;
    }

    private function addDataEmissao(?string $dataEmissao = null): void
    {
        if ($dataEmissao) {
            $this->data['dataEmissao'] = (new DateTimeImmutable($dataEmissao))->format('d.m.Y');
        }
    }
}