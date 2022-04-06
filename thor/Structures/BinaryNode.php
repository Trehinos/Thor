<?php

namespace Thor\Structures;

class BinaryNode
{

    public function __construct(public mixed $value, public ?self $left = null, public ?self $right = null)
    {
    }

    public function hasLeft(): bool
    {
        return $this->left !== null;
    }

    public function hasRight(): bool
    {
        return $this->right !== null;
    }

    public function isLeaf(): bool
    {
        return !($this->hasLeft() || $this->hasRight());
    }

    public function has(mixed $value): bool
    {
        if ($this->isLeaf()) {
            return $value === $this->value;
        }

        return $this->left?->has($value) || $this->right?->has($value);
    }

    public function each(callable $callback, BinaryOrder $order = BinaryOrder::PREFIX): mixed
    {
        $left = $this->left?->each($callback, $order);
        $right = $this->right?->each($callback, $order);

        return match ($order) {
            BinaryOrder::PREFIX => $callback($this->value, $left, $right),
            BinaryOrder::INFIX => $callback($left, $this->value, $right),
            BinaryOrder::POSTFIX => $callback($left, $right, $this->value),
        };
    }

    public static function toArray(?BinaryNode $tree, BinaryOrder $order = BinaryOrder::PREFIX): array
    {
        if ($tree === null) {
            return [];
        }
        $left = static::toArray($tree->left, $order);
        $right = static::toArray($tree->right, $order);
        return match ($order) {
            BinaryOrder::PREFIX => array_merge([$tree->value], $left, $right),
            BinaryOrder::INFIX => array_merge($left, [$tree->value], $right),
            BinaryOrder::POSTFIX => array_merge($left, $right, [$tree->value]),
        };
    }

    public static function test(): static
    {
        return new static(
            1,
            new static(
                2,
                new static(4),
                new static(
                    5,
                    new static(7),
                    new static(8)
                )
            ),
            new static(
                       3,
                right: new static(6, new static(9))
            )
        );
    }

}
