<?php

namespace Thor\Tools\Structures;

use SplQueue;

/**
 *
 */

/**
 *
 */
class BinaryNode
{

    private ?self $left = null;
    private ?self $right = null;

    /**
     * @param mixed $value
     */
    public function __construct(public mixed $value)
    {
    }

    /**
     * @param mixed|null $left
     *
     * @return $this
     */
    public function left(mixed $left = null): static
    {
        if ($left instanceof self) {
            $this->left = $left;
        } elseif ($left !== null) {
            $this->left = new static($left);
        }
        return $this;
    }

    /**
     * @param mixed|null $right
     *
     * @return $this
     */
    public function right(mixed $right = null): static
    {
        if ($right instanceof self) {
            $this->right = $right;
        } elseif ($right !== null) {
            $this->right = new static($right);
        }
        return $this;
    }

    /**
     * @return $this|null
     */
    public function leftNode(): ?static
    {
        return $this->left;
    }

    /**
     * @return $this|null
     */
    public function rightNode(): ?static
    {
        return $this->right;
    }

    /**
     * @return bool
     */
    public function hasLeft(): bool
    {
        return $this->left !== null;
    }

    /**
     * @return bool
     */
    public function hasRight(): bool
    {
        return $this->right !== null;
    }

    /**
     * @return bool
     */
    public function isLeaf(): bool
    {
        return !($this->hasLeft() || $this->hasRight());
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function has(mixed $value): bool
    {
        if ($this->isLeaf()) {
            return $value === $this->value;
        }

        return $this->left?->has($value) || $this->right?->has($value);
    }

    /**
     * @param callable    $callback
     * @param BinaryOrder $order
     *
     * @return mixed
     */
    public function each(callable $callback, BinaryOrder $order = BinaryOrder::PRE): mixed
    {
        $left = $this->left?->each($callback, $order);
        $right = $this->right?->each($callback, $order);

        return match ($order) {
            BinaryOrder::PRE => $callback($this->value, $left, $right),
            BinaryOrder::IN => $callback($left, $this->value, $right),
            BinaryOrder::POST => $callback($left, $right, $this->value),
        };
    }

    /**
     * @param BinaryNode|null $tree
     * @param BinaryOrder     $order
     *
     * @return array
     */
    public static function depthArray(?BinaryNode $tree, BinaryOrder $order = BinaryOrder::PRE): array
    {
        if ($tree === null) {
            return [];
        }
        $left = static::depthArray($tree->left, $order);
        $right = static::depthArray($tree->right, $order);
        return match ($order) {
            BinaryOrder::PRE => array_merge([$tree->value], $left, $right),
            BinaryOrder::IN => array_merge($left, [$tree->value], $right),
            BinaryOrder::POST => array_merge($left, $right, [$tree->value]),
        };
    }

    /**
     * @param BinaryNode|null $tree
     *
     * @return array
     */
    public static function breadthArray(?BinaryNode $tree): array
    {
        if ($tree === null) {
            return [];
        }
        $ret = [];
        $queue = new SplQueue();
        $queue->enqueue($tree);
        while (!$queue->isEmpty()) {
            $node = $queue->dequeue();
            $ret[] = $node->value;
            if ($node->hasLeft()) {
                $queue->enqueue($node->left);
            }
            if ($node->hasRight()) {
                $queue->enqueue($node->right);
            }
        }

        return $ret;
    }

}
