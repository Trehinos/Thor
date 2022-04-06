<?php

namespace Thor\Web;

interface NodeInterface
{

    public function getParent(): ?Node;

    public function setAttribute(string $name, string|bool|null $value): void;

    public function getAttributes(): array;

    public function getAttribute(string $name): ?string;

    public function addChild(Node $node): void;

    public function getName(): string;

    public function setName(string $name): void;

    public function getData(): string;

    /**
     * @return Node[]
     */
    public function getChildren(): array;

    public function getHtml(): string;

    public function setData(mixed $data): void;

}
