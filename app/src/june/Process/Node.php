<?php

namespace JuNe\Process;
use Thor\Tools\Strings;

class Node
{

    /**
     * @var Node[]
     */
    private array $children;

    private ?Node $owner = null;

    /**
     * @param string $name
     * @param string $description
     * @param Priority $priority
     */
    public function __construct(
        public string $name,
        public string $description = '',
        public Priority $priority = Priority::AVG,
        public bool $enabled = true
    ) {
    }

    final public function getOwner(): Node
    {
        return $this->owner;
    }

    final public function addChild(Node $node): static
    {
        $node->owner = $this;
        $this->children[$node->priority->value][$node->name] = $node;
        $node->enter();
        return $this;
    }

    final public function findChild(string $pattern): ?Node
    {
        $head = '';
        $find = Strings::split($pattern, '/', $head);
        foreach ($this->getChildren() as $child) {
            if ($head === $child->name) {
                return $child->findChild($find);
            } elseif ($head === '' && $find === $child->name) {
                return $child;
            }
        }

        return null;
    }

    final public function hasChild(Node $node): bool
    {
        return isset($this->children[$node->priority->value][$node->name]);
    }

    final public function removeChild(Node $node): static
    {
        if ($this->hasChild($node)) {
            $node->owner = null;
            $this->children[$node->priority->value][$node->name] = null;
            unset($this->children[$node->priority->value][$node->name]);
        }
        $node->leave();
        return $this;
    }

    /**
     * @return  Node[]
     */
    final public function getChildren(): array
    {
        return array_reduce(
            $this->children,
            fn($carry, $item) => $carry === null ? $item : array_merge($carry, $item)
        );
    }

    public function enter(): void
    {
    }

    public function leave(): void
    {
    }

    public function input(mixed $instr = null): void
    {
    }

    public function output(): mixed
    {
        return null;
    }

    /**
     *  - begin()
     *  - foreach children -> child->process(delta)
     *  - complete()
     *  - end()
     *
     * @param float $delta
     *
     * @return void
     */
    final public function process(float $delta): void
    {
        if (!$this->enabled) {
            return;
        }
        $this->begin();
        foreach ($this->getChildren() as $child) {
            $child->process($delta);
        }
        $this->complete($delta);
        $this->end();
    }

    public function begin(): void
    {
    }

    public function complete(float $delta): void
    {
    }

    public function end(): void
    {
    }

}
