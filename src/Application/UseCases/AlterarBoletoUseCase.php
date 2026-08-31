<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Formatters\AlterarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\AlterarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Ports\HttpClientInterface;

class AlterarBoletoUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private AlterarBoletoFormatter $formatter,
        private AlterarBoletoResponseParser $responseParser,
    )
    { }

    /**
     * Envia os dados para a API do BB e altera um registro de cobrança.
     * 
     * @param AlterarBoletoDTO $dto Dados da alteração
     * @return array
     * 
     * @throws BBApiException
     */
    public function execute(AlterarBoletoDTO $dto): array
    {
        $numeroConvenio = new NumeroConvenioVO($dto->numeroConvenio);
        $nossoNumero = new NossoNumeroVO($dto->nossoNumero);

        $nossoNumeroFormatado = IdentificadorBoleto::create(
            convenio: $numeroConvenio,
            nossoNumero: $nossoNumero
        )->identificadorCompleto;

        $uri = '/cobrancas/v2/boletos/' . $nossoNumeroFormatado;

        $payload = $this->formatter->format($dto);

        $responseJson = $this->httpClient->patch($uri, $payload);
        return $this->responseParser->parse($responseJson);
    }
}