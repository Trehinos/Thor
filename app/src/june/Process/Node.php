<?php

namespace June\Process;

use Thor\Tools\Strings;

class Node
{

    /**
     * @var Node[]
     */
    private array $children = [];

    private ?Node $owner = null;

    /**
     * @param string $name
     * @param string $description
     * @param Priority $priority
     * @param bool $enabled
     */
    public function __construct(
        public string $name,
        public string $description = '',
        public Priority $priority = Priority::AVG,
        public bool $enabled = true
    ) {
    }

    public function getRepr(): string
    {
        return $this->description;
    }

    public function getDebug(int $level = 0): string
    {
        $r = $this->getRepr();
        $spaces = str_pad('', $level * 3, ' ');
        $type = basename(str_replace('\\', '/', static::class));
        $output = "{$spaces}• [$type:$this->name:$r]\n";
        foreach ($this->getChildren() as $child) {
            $output .= $child->getDebug($level + 1);
        }
        return $output;
    }

    final public function getOwner(): ?Node
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
        $children = [];
        krsort($this->children);
        foreach ($this->children as $p => $c) {
            foreach ($c as $child) {
                $children[] = $child;
            }
        }

        return $children;
    }

    final public function getPath(): string
    {
        $ownerPath = ($this->getOwner()?->getPath() ?? '') . '/';
        return "$ownerPath/$this->name";
    }

    public function enter(): void
    {
    }

    public function leave(): void
    {
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

    final public function init(): void
    {
        foreach ($this->getChildren() as $child) {
            $child->init();
        }
        $this->ready();
    }

    /**
     * Called AFTER all children has been init()
     *
     * @return void
     */
    public function ready(): void
    {
    }

    /**
     * @param Event $event
     *
     * @return bool true if disptach() MUST continue
     */
    final public function dispatch(Event $event): bool
    {
        $done = !$this->event($event);
        foreach ($this->getChildren() as $child) {
            if ($done) {
                break;
            }
            $done = !$child->dispatch($event);
        }
        return !$done;
    }

    /**
     * Called when an event has been dispatched on this Node's owner.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function event(Event $event): bool
    {
        return true;
    }

}
