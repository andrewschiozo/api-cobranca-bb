<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Application\UseCases;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\DetalharBoletoDTO;
use AndrewsChiozo\ApiCobrancaBb\Ports\FormatterInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Ports\ResponseParserInterface;

class DetalharBoletoUseCase
{
 
    public function __construct(
        private HttpClientInterface $httpClient,
        // private FormatterInterface $formatter,
        private ResponseParserInterface $responseParser
    )
    { }

    /**
     * @param string $numeroBoleto O número de identificação do boleto no BB.
     * @return array Dados detalhados e limpos do boleto.
     */
    public function execute(DetalharBoletoDTO $dto): array
    {
        $nossoNumero = "000" . $dto->numeroConvenio->numero . str_pad($dto->nossoNumero->nossoNumero, 10, '0', STR_PAD_LEFT);
        $uri = "/cobrancas/v2/boletos/{$nossoNumero}";
        
        $queryParams = [
            'numeroConvenio' => $dto->numeroConvenio->numero,
        ];

        $responseJson = $this->httpClient->get($uri, $queryParams);
        return $this->responseParser->parse($responseJson);
    }
}