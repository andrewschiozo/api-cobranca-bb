<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Application;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AutenticarDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\AlterarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\AutenticarUseCase;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\DetalharBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBb\Application\UseCases\RegistrarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBb\Domain\DTOs\Responses\BBHttpClientAuditoria;
use AndrewsChiozo\ApiCobrancaBb\Domain\DTOs\Responses\TokenResponseDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters\AlterarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters\AutenticarFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters\RegistrarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\AlterarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\AutenticarResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\DetalharBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\RegistrarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters\BBHttpClientAdapter;

/**
 * Serviço de Fachada responsável por orquestrar a lógica de Cobranças.
 */
class CobrancaManagerFacade
{
    /**
     * Cria um novo Serviço de Fachada de Cobranças.
     * 
     * @param BBHttpClientAdapter $httpClient Cliente HTTP para comunicação com a API do BB.
     */
    public function __construct(
        private BBHttpClientAdapter $httpClient,
    ) { }

    public function autenticar(AutenticarDTO $dto): TokenResponseDTO
    {
        $useCase = new AutenticarUseCase(
            httpClient: $this->httpClient,
            formatter: new AutenticarFormatter(),
            responseParser: new AutenticarResponseParser()
        );
        return $useCase->execute($dto);
    }

    public function withAuth(string $token, string $appKey): self
    {
        $clone = clone $this;
        $clone->httpClient = $this->httpClient->withAuth($token, $appKey);
        return $clone;
    }

    public function ultimaAuditoria(): BBHttpClientAuditoria
    {
        return $this->httpClient->lastAudit();
    }

    /**
     * Envia os dados para a API do BB e registra uma nova cobrança.
     * 
     * @param RegistrarBoletoDTO $dto Dados da cobrança
     * @return array Retorna os dados da Cobrança criada
     * @throws BBApiException
     */
    public function registrarCobranca(RegistrarBoletoDTO $dto): array
    {
        $useCase = new RegistrarBoletoUseCase(
            httpClient: $this->httpClient,
            formatter: new RegistrarBoletoFormatter(),
            responseParser: new RegistrarBoletoResponseParser()
        );
        return $useCase->execute($dto);
    }

    /**
     * Detalha uma cobrança.
     * 
     * @param DetalharBoletoDTO $dto
     * @return array
     * @throws BBApiException
     */
    public function detalharCobranca(DetalharBoletoDTO $dto): array
    {
        $usecase = new DetalharBoletoUseCase(
            httpClient: $this->httpClient,
            responseParser: new DetalharBoletoResponseParser()
        );
        return $usecase->execute($dto);
    }

    /**
     * Altera uma cobrança.
     * 
     * @param AlterarBoletoDTO $dto Dados da cobrança a ser alterada.
     * @return array
     * @throws BBApiException
     */
    public function alterarCobranca(AlterarBoletoDTO $dto): array
    {
        $useCase = new AlterarBoletoUseCase(
            httpClient: $this->httpClient,
            formatter: new AlterarBoletoFormatter(),
            responseParser: new AlterarBoletoResponseParser()
        );
        return $useCase->execute($dto);
    }
}