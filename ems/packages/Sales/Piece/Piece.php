<?php

namespace Ems\Packages\Sales\Piece;

use DateTimeInterface;
use Ems\Packages\Sales\Element\Identifier;
use Ems\Packages\Sales\Element\Item;

class Piece
{

    /**
     * @var Item[]
     */
    private array $items;

    public function __construct(
        public Identifier $id,
        public string $reference,
        public DateTimeInterface $date,
        public DateTimeInterface $due,
    ) {
    }

    public function add(Item $item): void
    {
        $this->items[] = $item;
    }

    public function get(int $index): ?Item
    {
        return $this->items[$index] ?? null;
    }

    public function getItems(): array
    {
        return $this->items;
    }

}
