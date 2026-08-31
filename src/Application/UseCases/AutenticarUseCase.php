<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\AutenticarDTO;
use AndrewsChiozo\ApiCobrancaBB\Domain\DTOs\Responses\TokenResponseDTO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\AutenticarResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\AutenticarFormatter;

class AutenticarUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private AutenticarFormatter $formatter,
        private AutenticarResponseParser $responseParser
    )
    { }

    public function execute(AutenticarDTO $dto): TokenResponseDTO
    {
        $payload = $this->formatter->payload($dto->scope);
        $headers = $this->formatter->header($dto->clientId, $dto->clientSecret);

        $response = $this->httpClient->auth(
            uri: $dto->authUrl,
            payload: $payload,
            headers: $headers
        );
        return $this->responseParser->parse($response);
    }
}