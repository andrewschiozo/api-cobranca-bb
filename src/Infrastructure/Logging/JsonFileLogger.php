<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class JsonFileLogger extends AbstractLogger implements LoggerInterface
{
    
    public function __construct(
        private string $logFilePath
    )
    {
        $dir = dirname($logFilePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public function log($level, $message, array $context = []): void
    {
        if (!in_array($level, $this->getValidLevels())) {
            return;
        }

        $record = [
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s.u'),
            'level' => $level,
            'message' => $this->interpolate($message, $context),
            'context' => $context,
        ];

        $logLine = json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        file_put_contents($this->logFilePath, $logLine, FILE_APPEND);
    }

    /**
     * Substitui os placeholders da mensagem com os valores do contexto (PSR-3).
     */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            // Verifica se o valor pode ser convertido para string
            if (is_array($val) || (is_object($val) && !method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = json_encode($val);
            } else {
                $replace['{' . $key . '}'] = (string) $val;
            }
        }

        return strtr($message, $replace);
    }

     private function getValidLevels(): array
     {
        return [
            LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL, 
            LogLevel::ERROR, LogLevel::WARNING, LogLevel::NOTICE, 
            LogLevel::INFO, LogLevel::DEBUG
        ];
     }
}