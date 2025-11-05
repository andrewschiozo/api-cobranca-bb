<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Ports;

interface TokenStorageInterface
{
    /**
     * Busca o token no armazenamento.
     * @return string|null O token, se for válido (não expirado), ou null.
     */
    public function getToken(): ?string;

    /**
     * Armazena o token e seu tempo de expiração (calculado a partir do expiresInSeconds).
     */
    public function saveToken(string $token, int $expiresInSeconds): void;
}