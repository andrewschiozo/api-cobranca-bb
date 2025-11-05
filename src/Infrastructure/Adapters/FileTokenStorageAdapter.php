<?php
declare(strict_types=1);

namespace Andrewschiozo\ApiCobrancaBb\Infrastructure\Adapters;

use AndrewsChiozo\ApiCobrancaBb\Ports\TokenStorageInterface;

/**
 * Adapter concreto que armazena o token e o timestamp de expiração
 * em um arquivo JSON no disco.
 */
class FileTokenStorageAdapter implements TokenStorageInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
        }
    }

    public function getToken(): ?string
    {
        if (!file_exists($this->filePath)) {
            return null;
        }

        $content = file_get_contents($this->filePath);
        if ($content === false) {
            return null;
        }
        
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return null; 
        }

        if (empty($data['access_token']) || empty($data['expires_at'])) {
            return null;
        }

        if (time() >= $data['expires_at']) {
            return null;
        }

        return $data['access_token'];
    }

    public function saveToken(string $token, int $expiresInSeconds): void
    {
        $expirationTime = time() + $expiresInSeconds; 
        
        $data = [
            'access_token' => $token,
            'expires_at' => $expirationTime - 60, // renovar antes do tempo limite real
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        if (file_put_contents($this->filePath, $json, LOCK_EX) === false) {
            throw new \RuntimeException("Não foi possível salvar o token no cache: {$this->filePath}");
        }
    }
}