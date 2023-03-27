<?php

namespace Thor\Ngine;

use Thor\Tools\Strings;

class Node
{

    /**
     * @var Node[]
     */
    protected array $children = [];
    public readonly int $id;

    static private int $current_id = 0;

    public function __construct(public readonly string $name)
    {
        $this->id = self::$current_id++;
    }

    final public function addChild(Node $child): void
    {
        $this->children[$child->name] = $child;
        $child->onAdded($this);
        $this->onTreeUpdated($child);
    }

    final public function removeChild(string $name): void
    {
        if ($this->hasChild($name)) {
            $child = $this->children[$name];
            $this->children[$name] = null;
            unset($this->children[$name]);
            $child->onRemoved($this);
            $this->onTreeUpdated($child);
        }
    }

    final public function getChild(string $name): ?Node
    {
        return $this->children[$name] ?? null;
    }

    final public function hasChild(string $name): bool
    {
        return array_key_exists($name, $this->children);
    }

    /**
     * For each child of this node, call :
     *
     * ```php
     * $f($child);
     * ```
     *
     * Children can be filtered by instances of class $class.
     *
     * @template T
     * @template-extends Node
     *
     * @param callable    $f
     * @param class-string<T>|null $class
     *
     * @return void
     */
    final public function each(callable $f, ?string $class = null): void
    {
        /** @var T $child */
        foreach ($this->children as $child) {
            if ($class !== null && !($child instanceof $class)) {
                continue;
            }
            $f($child);
        }
    }

    final public function search(string $pattern): ?Node
    {
        $cursor = $this;
        $found = null;
        while ($found === null) {
            $tail = '';
            $name = Strings::token($pattern, '/', $tail);
            $cursor = $cursor->getChild($name);
            if ($cursor === null) {
                break;
            }
            if ($pattern === $name) {
                $found = $cursor;
            }
        }

        return $found;
    }

    final public function load(): void
    {
        $this->onLoad();
        $this->each(fn(Node $child) => $child->load());
        $this->onLoaded();
    }

    public function debugString(): string
    {
        $c = basename(str_replace('\\', '/', $this::class));
        return "{$this->id}:{$c}[{$this->name}]";
    }

    public function onLoad(): void
    {
    }

    public function onTreeUpdated(Node $node): void
    {
    }

    public function onLoaded(): void
    {
    }

    public function onAdded(Node $parent): void
    {
    }

    public function onRemoved(Node $parent): void
    {
    }

}
