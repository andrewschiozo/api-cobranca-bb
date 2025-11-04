<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Application;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoRapidoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\DetalharBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\RegistrarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;

/**
 * Serviço de Fachada responsável por orquestrar a lógica de Cobranças.
 */
class CobrancaManagerFacade
{
    /**
     * Cria um novo Serviço de Fachada de Cobranças.
     * 
     * @param \AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface $httpClient
     * @param \AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface $formatter
     * @param \AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface $responseParser
     */
    public function __construct(
        private RegistrarBoletoUseCase $registrarBoletoUseCase,
        private DetalharBoletoUseCase $detalharBoletoUseCase
    ) { }

    /**
     * Envia os dados para a API do BB e registra uma nova cobrança.
     * 
     * @param array $cobrancaData Dados da cobrança
     * @return array Retorna os dados da Cobrança criada
     * @throws HttpCommunicationException Se houver falha na comunicação.
     */
    public function emitirCobranca(array $cobrancaData): array
    {
        $dto = RegistrarBoletoRapidoDTO::fromArray($cobrancaData);
        return $this->registrarBoletoUseCase->execute($dto);
    }

    /**
     * Detalha uma cobrança.
     * 
     * @param array $data Dados da cobrança a ser detalhada. Ex: ['nossoNumero' => '1234567890', 'numeroConvenio' => '12345']
     * @return array
     * @throws HttpCommunicationException
     */
    public function detalharCobranca(array $data): array
    {
        $dto = DetalharBoletoDTO::fromArray($data);
        return $this->detalharBoletoUseCase->execute($dto);
    }
}