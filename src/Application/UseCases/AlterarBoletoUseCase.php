<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Application\DTO\RegistrarBoletoRapidoDTO;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\LoggerFactory;
use AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface;

class AlterarBoletoUseCase
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
     * @param AlterarBoletoDTO $alterarData Dados da alteração
     * @return array Retorna os dados da alteração
     * 
     * @throws HttpCommunicationException Se houver falha na comunicação.
     */
    public function execute(AlterarBoletoDTO $alterarData): array
    {
        $logger = $this->loggerFactory->createLogger('AlterarBoletoUseCase');
        $logger->info('Início');

        $payload = $this->formatter->format($alterarData);
        $uri = '/cobrancas/v2/boletos/' . $alterarData->numeroBoleto->identificadorCompleto;

        try{
            $responseJson = $this->httpClient->patch($uri, $payload, [], $logger);
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