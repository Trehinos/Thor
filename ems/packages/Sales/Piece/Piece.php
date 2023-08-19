<?php

namespace Ems\Packages\Sales\Piece;

use DateTimeInterface;
use Ems\Packages\Sales\Element\Item;
use Ems\Packages\Sales\Element\Contact;
use Ems\Packages\Sales\Element\Identifier;

class Piece
{

    /**
     * @var Line[]
     */
    private array $lines;

    private int $lineIndex = 0;

    public function __construct(
        public Identifier $id,
        public string $reference,
        public DateTimeInterface $date,
        public DateTimeInterface $due,
        public ?Contact $emitter = null,
        public ?Contact $receiver = null,
    ) {
    }

    public function add(Line $line): void
    {
        $this->lines[++$this->lineIndex] = $line;
    }

    public function get(int $index): ?Line
    {
        return $this->lines[$index] ?? null;
    }

    public function all(): array
    {
        return $this->lines;
    }

}
