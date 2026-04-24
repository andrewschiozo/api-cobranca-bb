<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Application;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoRapidoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\AlterarBoletoUseCase;
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
     * @param RegistrarBoletoUseCase $registrarBoletoUseCase
     * @param DetalharBoletoUseCase $detalharBoletoUseCase
     * @param AlterarBoletoUseCase $alterarBoletoUseCase
     */
    public function __construct(
        private RegistrarBoletoUseCase $registrarBoletoUseCase,
        private DetalharBoletoUseCase $detalharBoletoUseCase,
        private AlterarBoletoUseCase $alterarBoletoUseCase
    ) { }

    /**
     * Envia os dados para a API do BB e registra uma nova cobrança.
     * 
     * @param RegistrarBoletoRapidoDTO $dto Dados da cobrança
     * @return array Retorna os dados da Cobrança criada
     * @throws HttpCommunicationException Se houver falha na comunicação.
     */
    public function emitirCobrancaRapida(RegistrarBoletoRapidoDTO $dto): array
    {
        return $this->registrarBoletoUseCase->execute($dto);
    }

    /**
     * Detalha uma cobrança.
     * 
     * @param DetalharBoletoDTO $dto
     * @return array
     * @throws HttpCommunicationException
     */
    public function detalharCobranca(DetalharBoletoDTO $dto): array
    {
        return $this->detalharBoletoUseCase->execute($dto);
    }

    /**
     * Altera uma cobrança.
     * 
     * @param AlterarBoletoDTO $dto Dados da cobrança a ser alterada.
     * @return array
     * @throws HttpCommunicationException
     */
    public function alterarCobranca(AlterarBoletoDTO $dto): array
    {
        return $this->alterarBoletoUseCase->execute($dto);
    }
}