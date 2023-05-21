<?php

namespace Thor\Http\Web;

/**
 *
 */

/**
 *
 */
final class TextNode extends Node
{

    /**
     * @param string    $data
     * @param Node|null $parent
     */
    public function __construct(protected string $data, ?Node $parent = null)
    {
        parent::__construct('', $parent);
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->getData();
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return [];
    }

    /**
     * @param Node $node
     *
     * @return void
     */
    public function addChild(Node $node): void
    {
    }

    /**
     * @param string           $name
     * @param string|bool|null $value
     *
     * @return void
     */
    public function setAttribute(string $name, string|bool|null $value): void
    {
    }

    /**
     * @param string $name
     *
     * @return void
     */
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

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

}
