<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoRapidoDTO;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\LoggerFactory;
use AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface;

class RegistrarBoletoUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private FormatterInterface $formatter,
        private ResponseParserInterface $responseParser,
        private LoggerFactory $loggerFactory
    )
    { }

    /**
     * Envia os dados para a API do BB e registra uma nova cobrança.
     * 
     * @param RegistrarBoletoRapidoDTO $cobrancaData Dados da cobrança
     * @return array Retorna os dados da Cobrança criada
     * 
     * @throws HttpCommunicationException Se houver falha na comunicação.
     */
    public function execute(RegistrarBoletoRapidoDTO $cobrancaData): array
    {
        $logger = $this->loggerFactory->createLogger('RegistrarBoletoUseCase');
        $logger->info('Início');

        $payload = $this->formatter->format($cobrancaData);
        $uri = '/cobrancas/v2/boletos';

        try{
            $responseJson = $this->httpClient->post($uri, $payload, [], $logger);
            $response = $this->responseParser->parse($responseJson);

            $logger->info('Fim c/ sucesso');
            return $response;
        } catch (HttpCommunicationException $e){
            $logger->critical('Fim c/ falha', [
                'exception_message' => $e->getMessage(),
                'http_code' => $e->getCode()
            ]);
            throw $e;
        }
    }
}