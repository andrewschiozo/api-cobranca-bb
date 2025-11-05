<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Logger que armazena mensagens em um buffer (array) na memória e só as escreve
 * para o logger final (Logger de destino) quando o método 'flush()' é chamado.
 */
class BufferedLogger extends AbstractLogger implements BufferedLoggerInterface
{
    private LoggerInterface $logger;
    private array $buffer = [];

    /**
     * @param LoggerInterface $logger O Logger que fará a escrita efetiva no disco.
     * @param bool $flushOnShutdown Se TRUE, o buffer é descarregado automaticamente no final da execução PHP.
     */
    public function __construct(LoggerInterface $logger, bool $flushOnShutdown = false)
    {
        $this->logger = $logger;

        if ($flushOnShutdown) {
            register_shutdown_function([$this, 'flush']);
        }
    }

    public function log($level, $message, array $context = []): void
    {
        $this->buffer[] = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => microtime(true)
        ];
    }

    /**
     * Descarrega o buffer, escrevendo todas as mensagens acumuladas no Logger.
     */
    public function flush(): void
    {
        if (empty($this->buffer)) {
            return;
        }

        foreach ($this->buffer as $entry) {
            $this->logger->log($entry['level'], $entry['message'], $entry['context']);
        }

        $this->buffer = [];
    }
}