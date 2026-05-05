<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters\RegistrarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\RegistrarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Ports\HttpClientInterface;

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
     * @throws HttpCommunicationException Se houver falha na comunicação.
     */
    public function execute(RegistrarBoletoDTO $cobrancaData): array
    {
        $payload = $this->formatter->format($cobrancaData);
        $uri = '/cobrancas/v2/boletos';

        $responseJson = $this->httpClient->post($uri, $payload);
        $response = $this->responseParser->parse($responseJson);

        return $response;
    }
}