<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\IdentificadorBoleto;
use AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface;

class DetalharBoletoUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        private ResponseParserInterface $responseParser
    )
    { }

    /**
     * @param string $numeroBoleto O número de identificação do boleto no BB.
     * @return array Dados detalhados e limpos do boleto.
     */
    public function execute(DetalharBoletoDTO $dto): array
    {
        $nossoNumeroFormatado = IdentificadorBoleto::create($dto->numeroConvenio, $dto->nossoNumero)->identificadorCompleto;
        $uri = "/cobrancas/v2/boletos/{$nossoNumeroFormatado}";
        
        $queryParams = [
            'numeroConvenio' => $dto->numeroConvenio->numero,
        ];

        $responseJson = $this->httpClient->get($uri, $queryParams);
        return $this->responseParser->parse($responseJson);
    }
}