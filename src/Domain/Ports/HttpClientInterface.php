<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Ports;

interface HttpClientInterface
{
    /**
     * @return string
     */
    public function auth(string $uri, array $payload, array $headers = []): string;

    /**
     * @return string
     */
    public function get(string $uri, array $queryParams = [], array $headers = []): string;

    /**
     * @return string
     */
    public function post(string $uri, array $payload, array $headers = []): string;

    /**
     * @return string
     */
    public function patch(string $uri, array $payload, array $headers = []): string;
}