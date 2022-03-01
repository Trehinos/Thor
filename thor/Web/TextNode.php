<?php

namespace Thor\Web;

final class TextNode extends Node
{

    public function __construct(protected string $data, ?Node $parent = null)
    {
        parent::__construct('', $parent);
    }

    public function getHtml(): string
    {
        return $this->getData();
    }

    public function getChildren(): array
    {
        return [];
    }

    public function addChild(Node $node): void
    {
    }

    public function setAttribute(string $name, string|bool|null $value): void
    {
    }

    public function setName(string $name): void
    {
    }

    /**
     * @param string $data
     */
    public function setData(mixed $data): void
    {
        if (!is_string($data)) {
            return;
        }
        $this->data = $data;
    }

    public function getData(): string
    {
        return $this->data;
    }

}
