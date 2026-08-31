<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBB\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBB\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Services\Parsers\DetalharBoletoResponseParser;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\NossoNumeroVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\ValueObjects\NumeroConvenioVO;
use AndrewsChiozo\ApiCobrancaBB\Domain\Ports\HttpClientInterface;

class DetalharBoletoUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private DetalharBoletoResponseParser $responseParser,
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

        $responseJson = $this->httpClient->get(
            "/cobrancas/v2/boletos/{$nossoNumeroFormatado}",
            ['numeroConvenio' => $numeroConvenio->numero]
        );

        return $this->responseParser->parse($responseJson);
    }
}