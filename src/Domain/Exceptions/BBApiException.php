<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions;

use Throwable;

class BBApiException extends \Exception
{
    private array $apiResponse;

    public function __construct(string $message, int $httpCode, array $apiResponse, ?Throwable $previous = null)
    {
        parent::__construct($message, $httpCode, $previous);
        $this->apiResponse = $apiResponse;
    }

    public function getApiResponse(): array
    {
        return $this->apiResponse;
    }
}