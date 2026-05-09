<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters;

/**
 * Serviço responsável por formatar os dados para a autenticação
 * no padrão exigido pela API do Banco do Brasil.
 */
class AutenticarFormatter
{
    /**
     * @param string $clientId
     * @param string $clientSecret
     * @return array
     */
    public function header(string $clientId, string $clientSecret): array
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
    }

    /**
     * @param string $scope
     * @return array
     */
    public function payload(string $scope): array
    {
        return [
            'grant_type' => 'client_credentials',
            'scope' => $scope
        ];
    }
}
