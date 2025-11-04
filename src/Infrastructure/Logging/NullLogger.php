<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Logger que não faz nada. Ideal para quando o usuário não configura um logger.
 * Evita que o SDK lance exceções se o logger for null.
 */
class NullLogger implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = []): void
    {

    }
}