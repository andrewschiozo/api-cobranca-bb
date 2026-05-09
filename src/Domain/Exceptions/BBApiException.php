<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Exceptions;

use AndrewsChiozo\ApiCobrancaBb\Domain\DTOs\Responses\BBHttpClientAuditoria;
use Exception;
use Throwable;

class BBApiException extends Exception
{
    private BBHttpClientAuditoria $auditoria;

    public function __construct(string $message, int $httpCode, BBHttpClientAuditoria $auditoria, ?Throwable $previous = null)
    {
        parent::__construct($message, $httpCode, $previous);
        $this->auditoria = $auditoria;
    }

    public function getAuditoria(): BBHttpClientAuditoria
    {
        return $this->auditoria;
    }
}