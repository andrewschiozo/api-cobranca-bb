<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\RegistrarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\RegistrarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\Ports\HttpClientInterface;

class RegistrarBoletoUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private RegistrarBoletoFormatter $formatter,
        private RegistrarBoletoResponseParser $responseParser
    )
    { }

    /**
     * Envia os dados para a API do BB e registra uma nova cobrança.
     * 
     * @param RegistrarBoletoDTO $cobrancaData Dados da cobrança
     * @return array Retorna os dados da Cobrança criada
     * 
     * @throws BBApiException
     */
    public function execute(RegistrarBoletoDTO $cobrancaData): array
    {
        $payload = $this->formatter->format($cobrancaData);
        $uri = '/cobrancas/v2/boletos';

        $response = $this->httpClient->post($uri, $payload);
        return $this->responseParser->parse($response);
    }
}