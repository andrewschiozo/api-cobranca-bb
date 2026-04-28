<?php

declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Services\Formatters;

use AndrewsChiozo\ApiCobrancaBb\Application\DTO\AutenticarDTO;
/**
 * Serviço responsável por formatar os dados para a autenticação
 * no padrão exigido pela API do Banco do Brasil.
 */
class AutenticarFormatter
{
    /**
     * Transforma os dados da Autenticação em um array compatível com o payload da API.
     * @param AutenticarDTO $dto
     * @return array Payload pronto para ser enviado via HTTP
     */
    public function format(AutenticarDTO $dto): array
    {
        return [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($dto->clientId . ':' . $dto->clientSecret),
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
                'scope' => $dto->scope
            ]
        ];
    }
}
