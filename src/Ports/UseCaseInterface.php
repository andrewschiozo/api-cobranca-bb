<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Ports;

interface UseCaseInterface
{
    public function execute(?array $data = null): array;
}