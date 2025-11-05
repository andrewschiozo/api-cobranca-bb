<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Adapters;

use AndrewsChiozo\ApiCobrancaBb\Ports\TokenStorageInterface;

/**
 * Adapter de Mock para testar o fluxo de token sem acessar disco ou cache.
 */
class MockTokenStorageAdapter implements TokenStorageInterface
{
    private ?string $token = null;
    private int $expirationTime = 0;

    public function getToken(): ?string
    {
        if ($this->token === null) {
            return null;
        }

        if (time() >= $this->expirationTime) {
            $this->token = null;
            $this->expirationTime = 0;
            return null;
        }

        return $this->token;
    }

    public function saveToken(string $token, int $expiresInSeconds): void
    {
        $this->token = $token;
        $this->expirationTime = time() + $expiresInSeconds; 
    }
}