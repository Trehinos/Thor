<?php

namespace Thor\Web;

/**
 *
 */

/**
 *
 */
class Node implements NodeInterface
{

    private array $attributes = [];
    private array $children = [];

    /**
     * @param string    $name
     * @param Node|null $parent
     */
    public function __construct(
        private string $name,
        private ?Node $parent = null
    ) {
    }

    /**
     * @return Node|null
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * @param string           $name
     * @param string|bool|null $value
     *
     * @return void
     */
    public function setAttribute(string $name, string|bool|null $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getAttribute(string $name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param Node $node
     *
     * @return void
     */
    public function addChild(Node $node): void
    {
        $this->children[] = $node;
        $node->parent = $this;
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

    /**
     * @return string
     */
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
