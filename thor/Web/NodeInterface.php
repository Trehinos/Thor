<?php

namespace Thor\Web;

/**
 *
 */

/**
 *
 */
interface NodeInterface
{

    /**
     * @return Node|null
     */
    public function getParent(): ?Node;

    /**
     * @param string           $name
     * @param string|bool|null $value
     *
     * @return void
     */
    public function setAttribute(string $name, string|bool|null $value): void;

    /**
     * @return array
     */
    public function getAttributes(): array;

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getAttribute(string $name): ?string;

    /**
     * @param Node $node
     *
     * @return void
     */
    public function addChild(Node $node): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return string
     */
    public function getData(): string;

    /**
     * @return Node[]
     */
    public function getChildren(): array;

    /**
     * @return string
     */
    public function getHtml(): string;

    /**
     * @param mixed $data
     *
     * @return void
     */
    public function setData(mixed $data): void;

}
