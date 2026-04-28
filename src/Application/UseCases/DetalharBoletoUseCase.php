<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\DetalharBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\HttpCommunicationException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Ports\HttpClientInterface;
use Psr\Log\LoggerInterface;

class DetalharBoletoUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private DetalharBoletoResponseParser $responseParser,
        private LoggerInterface $logger
    )
    { }

    /**
     * @param DetalharBoletoDTO $dto Dto contendo os dados para detalhar o boleto.
     * @return array Dados detalhados e limpos do boleto.
     */
    public function execute(DetalharBoletoDTO $dto): array
    {
        $numeroConvenio = new NumeroConvenioVO($dto->numeroConvenio);
        $nossoNumero = new NossoNumeroVO($dto->nossoNumero);

        $nossoNumeroFormatado = IdentificadorBoleto::create(
            convenio: $numeroConvenio,
            nossoNumero: $nossoNumero
        )->identificadorCompleto;

        try{
            $responseJson = $this->httpClient->get(
                "/cobrancas/v2/boletos/{$nossoNumeroFormatado}",
                ['numeroConvenio' => $numeroConvenio->numero]
            );

            return $this->responseParser->parse($responseJson);
        } catch (HttpCommunicationException $e){
            $this->logger->critical('Fim c/ falha', [
                'exception_message' => $e->getMessage(),
                'http_code' => $e->getCode()
            ]);

            throw $e;
        }
    }
}