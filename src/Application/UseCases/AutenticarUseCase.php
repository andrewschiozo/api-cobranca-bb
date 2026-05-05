<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AutenticarDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\TokenResponseDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters\AutenticarFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\AutenticarResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Ports\HttpClientInterface;

class AutenticarUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private AutenticarFormatter $formatter,
        private AutenticarResponseParser $responseParser
    )
    { }

    /**
     * Envia os dados para a API do BB para autenticação.
     * 
     * @param AutenticarDTO $dto Dados para autenticação
     * @return TokenResponseDTO Retorna os dados da autenticação
     * 
     * @throws HttpCommunicationException Se houver falha na comunicação.
     */
    public function execute(AutenticarDTO $dto): TokenResponseDTO
    {
        $request = $this->formatter->format($dto);

        $responseJson = $this->httpClient->sendRequest('POST', $dto->authUrl, $request);
        return $this->responseParser->parse($responseJson);
    }
}