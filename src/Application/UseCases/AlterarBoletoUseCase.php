<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AlterarBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions\BBApiException;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters\AlterarBoletoFormatter;
use AndrewsChiozo\ApiCobrancaBb\Domain\Services\Parsers\AlterarBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBb\Domain\Ports\HttpClientInterface;

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