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
     */
    public function __construct(
        private HttpClientInterface $httpClient
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
        $payload = $cobrancaData;

        $uri = '/cobrancas/v2/boletos';
        $responseJson = $this->httpClient->post($uri, $payload);
        $responseData = json_decode($responseJson, true);

        return $responseData;
    }
}