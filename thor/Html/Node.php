<?php

namespace Thor\Html;

class Node implements NodeInterface
{

    private array $attributes = [];
    private array $children = [];

    public function __construct(
        private string $name,
        private ?Node $parent = null
    ) {
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }

    public function setAttribute(string $name, string|bool|null $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    public function addChild(Node $node): void
    {
        $this->children[] = $node;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        $innerHtml = '';
        foreach ($this->getChildren() as $child) {
            $innerHtml .= $child->getHtml();
        }
        return $innerHtml;
    }

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getHtml(): string
    {
        $attributes = implode(
            ' ',
            array_filter(
                array_map(
                    fn(string $key, $value) => match (true) {
                        $value === true => $key,
                        $value === null, $value === false => null,
                        default => "$key=\"$value\""
                    },
                    array_keys($this->attributes),
                    array_values($this->attributes)
                )
            )
        );
        if ($attributes !== '') {
            $attributes = " $attributes";
        }
        if (empty($this->children)) {
            return "<{$this->name}$attributes>";
        }

        return "<{$this->name}$attributes>{$this->getData()}</$this->name>";
    }

    /**
     * @param array $data
     */
    public function setData(mixed $data): void
    {
        if (!is_array($data)) {
            return;
        }
        $this->children = $data;
    }

}
