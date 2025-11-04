<?php 
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging;

use Psr\Log\LoggerInterface;

class LoggerFactory
{
    private string $logDir;

    public function __construct(string $logDir)
    {
        $this->logDir = rtrim($logDir, '/');

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
    }

    /**
     * Cria um Logger com buffer garantindo um arquivo único (timestamp_contextname_uuid.log) para rastreamento.
     */
    public function createLogger(string $contextName): LoggerInterface
    {
        $uuid = bin2hex(random_bytes(8)); 

        $timestamp = (new \DateTime('now'))->format('YmdHis');
        $safeName = str_replace(['\\', '/'], '-', $contextName);
        
        $filename = "{$timestamp}_{$safeName}_{$uuid}.log";
        $logFilePath = "{$this->logDir}/{$filename}";

        $targetLogger = new JsonFileLogger($logFilePath);

        return new BufferedLogger($targetLogger, $flushOnShutdown = true);
    }
}