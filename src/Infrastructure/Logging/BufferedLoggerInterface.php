<?php
declare(strict_types=1);

namespace AndrewsChiozo\ApiCobrancaBb\Infrastructure\Logging;

/**
* Descarrega o buffer
*/
interface BufferedLoggerInterface
{
    public function flush(): void;
}