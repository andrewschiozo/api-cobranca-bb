<?php 

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services;

use AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface;
use AndrewsChiozo\ApiCobrancaBb\Exceptions\HttpCommunicationException;

/**
 * Serviço de Domínio responsável por orquestrar a lógica de Cobranças.
 */
class CobrancaManager
{
    /**
     * Cria um novo Serviço de Cobranças.
     * 
     * @param \AndrewsChiozo\ApiCobrancaBb\Ports\HttpClientInterface $httpClient
     * @param \AndrewsChiozo\ApiCobrancaBb\Domain\Services\CobrancaFormatter $formatter
     * @param \AndrewsChiozo\ApiCobrancaBb\Domain\Services\CobrancaResponseParser $responseParser
     */
    public function __construct(
        private HttpClientInterface $httpClient,
        private CobrancaFormatter $formatter,
        private CobrancaResponseParser $responseParser
    ) { }

    /**
     * Envia os dados para a API do BB e registra uma nova cobrança.
     * 
     * @param array $cobrancaData Dados da cobrança
     * @return array Retorna os dados da Cobrança criada
     * @throws HttpCommunicationException Se houver falha na comunicação.
     */
    public function emitirCobranca(array $cobrancaData): array
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