<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Collections\DescontoCollection;
use AndrewsChiozo\ApiCobrancaBB\Domain\Enums\DescontoTipoEnum;
use AndrewsChiozo\ApiCobrancaBB\Domain\Enums\JurosMoraTipoEnum;
use AndrewsChiozo\ApiCobrancaBB\Domain\Enums\MultaTipoEnum;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\DescontoVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\DocumentoVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\JurosMoraVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\MultaVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\NumeroTituloBeneficiarioVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\PagadorVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\ValorAbatimentoVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\ValorTituloVO;
use DateTimeImmutable;

/**
 * Serviço responsável por formatar e validar os dados internos de uma Cobrança
 * no payload JSON exigido pela API do Banco do Brasil.
 */
class RegistrarBoletoFormatter
{
    private array $data;
    private const QUANTIDADE_DESCONTOS = 3;

    /**
     * Transforma os dados da Cobrança em um array compatível com o payload da API.
     * * @param array $cobrancaData Dados internos (ex: ['valor' => 100.50, 'cliente' => '...'])
     * @return array Payload pronto para ser enviado via HTTP
     */
    public function format(RegistrarBoletoDTO $dto): array
    {
        $convenio = new NumeroConvenioVO($dto->numeroConvenio);
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

        $this->data = [
            'numeroConvenio' => $convenio->numero, 
            'dataVencimento' => $dataVencimento->format('d.m.Y'),
            'valorOriginal' => $valortitulo->formatadoParaBB(),
            'pagador' => [
                'tipoInscricao' => $pagador->documento->tipo->value,
                'numeroInscricao' => $pagador->documento->valor,
                'cep' => $pagador->cep
            ],
        ];
        
        $this->addNossoNumero($convenio, $dto->nossoNumero);
        $this->addDataEmissao($dto->dataEmissao);
        $this->addValorAbatimento($dto->valorAbatimento);
        $this->addNumeroTituloBeneficiario($dto->numeroTituloBeneficiario);
        $this->addDescontos($dto);
        $this->addJurosMora($dto->jurosMoraTipo, $dto->jurosMoraValor);

        return $this->data;
    }

    private function addNossoNumero(NumeroConvenioVO $convenio, ?string $nossoNumero = null): void
    {
        if ($nossoNumero) {
            $nossoNumero = new NossoNumeroVO($nossoNumero);
            $numeroTituloCliente = IdentificadorBoleto::create($convenio, $nossoNumero)->identificadorCompleto;
            $this->data['numeroTituloCliente'] = $numeroTituloCliente;
        }
        
    }

    private function addDataEmissao(?string $dataEmissao = null): void
    {
        if ($dataEmissao) {
            $this->data['dataEmissao'] = (new DateTimeImmutable($dataEmissao))->format('d.m.Y');
        }
    }

    private function addValorAbatimento(?string $valorAbatimento = null): void
    {
        if ($valorAbatimento) {
            $this->data['valorAbatimento'] = (new ValorAbatimentoVO($valorAbatimento))->formatadoParaBB();
        }
    }

    private function addNumeroTituloBeneficiario(?string $numeroTituloBeneficiario = null): void
    {
        if ($numeroTituloBeneficiario) {
            $vo = new NumeroTituloBeneficiarioVO($numeroTituloBeneficiario);
            $this->data['numeroTituloBeneficiario'] = $vo->numeroTitulo;
        }
    }

    private function addDescontos(RegistrarBoletoDTO $dto)
    {
        $descontoCollection = $this->createDescontoCollection($dto);

        if (empty($descontoCollection->items)) {
            return;
        }

        $prefixos = ['desconto', 'segundoDesconto', 'terceiroDesconto'];
        $descontos = [];
        for ($i = 0; $i < self::QUANTIDADE_DESCONTOS; $i++) {
            $desconto = $descontoCollection->items[$i] ?? null;

            if ($desconto === null || $desconto->tipo === DescontoTipoEnum::SEM_DESCONTO) {
                continue;
            }

            $descontos[$prefixos[$i]] = $this->formatDesconto($desconto);
        }

        $this->data = array_merge($this->data, $descontos);
    }

    private function createDescontoCollection(RegistrarBoletoDTO $dto): DescontoCollection
    {
        $descontos = new DescontoCollection();
        $tipoPrimeiroDesconto = null;

        for ($i = 1; $i <= self::QUANTIDADE_DESCONTOS; $i++) {
            $tipo  = $dto->{'desconto' . $i . 'Tipo'} ?? null;

            if ($tipo === null) {
                continue;
            }

            // O primeiro desconto define o tipo de desconto para os demais
            if ($i === 1) {
                $tipoPrimeiroDesconto = $tipo;
            }

            $data  = $dto->{'desconto' . $i . 'Data'} ?? null;
            $valor = $dto->{'desconto' . $i . 'Valor'} ?? null;
            
            $descontos->add(new DescontoVO(
                DescontoTipoEnum::tryFromString($tipoPrimeiroDesconto ?? DescontoTipoEnum::SEM_DESCONTO->name),
                $valor,
                new DateTimeImmutable($data),
            ));
        }

        return $descontos;
    }

    private function formatDesconto(DescontoVO $desconto): array
    {
        $keyValor = $desconto->tipo === DescontoTipoEnum::PERCENTUAL_ATE_DATA ? "porcentagem" : "valor";
     
        $descontoFormatado = [
            "tipo"          => $desconto->tipo->value,
            "dataExpiracao" => $desconto->dataLimite->format('d.m.Y'),
            $keyValor       => $desconto->__toString()
        ];

        return $descontoFormatado;
    }

    private function addJurosMora(?string $tipo, ?string $valor): void
    {
        if (!$tipo) {
            return;
        }

        $jurosMoraVO = new JurosMoraVO(
            tipo: JurosMoraTipoEnum::tryFromString($tipo ?? JurosMoraTipoEnum::DISPENSAR->name),
            valor: $valor
        );

        $keyValor = $jurosMoraVO->tipo === JurosMoraTipoEnum::TAXA_MENSAL ? "porcentagem" : "valor";
    
        $this->data['jurosMora'] = [
            'tipo'      => $jurosMoraVO->tipo->value,
            $keyValor   => $jurosMoraVO->__toString()
        ];
    }

    private function addMulta(?string $tipo, ?string $valor, ?string $data): void
    {
        if (!$tipo) {
            return;
        }

        $multaVO = new MultaVO(
            tipo: MultaTipoEnum::tryFromString($tipo ?? MultaTipoEnum::SEM_MULTA->name),
            valor: $valor,
            data: new DateTimeImmutable($data)
        );

        $keyValor = $multaVO->tipo === MultaTipoEnum::PERCENTUAL ? "porcentagem" : "valor";
    
        $this->data['multa'] = [
            'tipo'      => $multaVO->tipo->value,
            $keyValor   => $multaVO->__toString(),
            'data'      => $multaVO->data->format('d.m.Y')
        ];
    }
}
