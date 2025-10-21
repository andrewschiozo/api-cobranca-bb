<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\UseCaseInterface;

class RegistrarBoletoUseCase implements UseCaseInterface
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private FormatterInterface $formatter,
        private ResponseParserInterface $responseParser
    )
    { }

        /**
     * Envia os dados para a API do BB e registra uma nova cobrança.
     * 
     * @param array $cobrancaData Dados da cobrança
     * @return array Retorna os dados da Cobrança criada
     * @throws HttpCommunicationException Se houver falha na comunicação.
     */
    public function execute(?array $cobrancaData = null): array
    {
        $payload = $this->formatter->format($cobrancaData);
        $uri = '/cobrancas/v2/boletos';

        try{
            $responseJson = $this->httpClient->post($uri, $payload);
        } catch (HttpCommunicationException $e){
            throw $e;
        }
        
        $response = $this->responseParser->parse($responseJson);

        return $response;
    }
}