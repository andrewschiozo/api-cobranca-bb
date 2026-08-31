<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Domain\DTOs\Responses;

readonly class BBHttpClientAuditoria
{
    public function __construct(
        public bool $success,
        public string $method,
        public string $uri,
        public ?array $headers,
        public ?string $payload,
        public int $statusCode,
        public ?string $response
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? false,
            method: $data['method'],
            uri: $data['uri'],
            headers: $data['headers'] ?? null,
            payload: $data['payload'] ?? null,
            statusCode: $data['statusCode'],
            response: $data['response'] ?? null,
        );
    }
}