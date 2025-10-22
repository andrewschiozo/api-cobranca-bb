<?php
declare(strict_types= 1);

namespace AndrewsChiozo\ApiCobrancaBb\Ports;

interface DTOInterface
{

    public static function fromArray(array $data): self;

}