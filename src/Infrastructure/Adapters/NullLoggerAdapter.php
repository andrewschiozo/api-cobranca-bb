<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBB\Infrastructure\Adapters;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Logger que não faz nada
 */
class NullLoggerAdapter implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = []): void
    {

    }
}