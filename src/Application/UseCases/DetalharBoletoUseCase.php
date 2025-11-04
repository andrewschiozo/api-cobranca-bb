<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging\LoggerFactory;
use AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface;

class DetalharBoletoUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private ResponseParserInterface $responseParser,
        private LoggerFactory $loggerFactory
    )
    { }

    /**
     * @param DetalharBoletoDTO $dto Dto contendo os dados para detalhar o boleto.
     * @return array Dados detalhados e limpos do boleto.
     */
    public function execute(DetalharBoletoDTO $dto): array
    {
        $logger = $this->loggerFactory->createLogger('DetalharBoletoUseCase');
        $logger->info('Início');

        $nossoNumeroFormatado = IdentificadorBoleto::create($dto->numeroConvenio, $dto->nossoNumero)->identificadorCompleto;
        $uri = "/cobrancas/v2/boletos/{$nossoNumeroFormatado}";
        
        $queryParams = [
            'numeroConvenio' => $dto->numeroConvenio->numero,
        ];
        try{
            $responseJson = $this->httpClient->get($uri, $queryParams, [], $logger);
            $logger->info('Fim c/ sucesso');
            return $this->responseParser->parse($responseJson);
        } catch (HttpCommunicationException $e){
            $logger->critical('Fim c/ falha', [
                'exception_message' => $e->getMessage(),
                'http_code' => $e->getCode()
            ]);
            throw $e;
        }
    }
}