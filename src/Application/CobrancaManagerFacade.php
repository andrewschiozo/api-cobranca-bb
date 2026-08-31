<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Application;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBB\Application\DTO\AutenticarDTO;
use AndrewsChiozo\ApiCobrancaBB\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBB\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBB\Application\UseCases\AlterarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBB\Application\UseCases\AutenticarUseCase;
use AndrewsChiozo\ApiCobrancaBB\Application\UseCases\DetalharBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBB\Application\UseCases\RegistrarBoletoUseCase;
use AndrewsChiozo\ApiCobrancaBB\Domain\DTOs\Responses\BBHttpClientAuditoria;
use AndrewsChiozo\ApiCobrancaBB\Domain\DTOs\Responses\TokenResponseDTO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\AlterarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\AutenticarFormatter;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\RegistrarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\AlterarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\AutenticarResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\DetalharBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\RegistrarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Infrastructure\Adapters\BBHttpClientAdapter;

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