<?php

namespace AndrewsChiozo\ApiCobrancaBb\Domain\Collections;

use AndrewsChiozo\ApiCobrancaBb\Domain\ValueObjects\DescontoVO;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

class DescontoCollection implements IteratorAggregate
{
    public private(set) array $items = [];

    public function __construct(DescontoVO ...$items)
    {
        $this->items = $items;
    }

    public function add(DescontoVO $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    // property hook / virtual count property
    public int $count {
        get => count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}