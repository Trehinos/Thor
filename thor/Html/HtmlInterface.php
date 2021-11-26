<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html;

interface HtmlInterface
{

    public function toHtml(): string;

    public function getAttr(string $name): mixed;

    public function setAttr(string $name, $value): void;

    public function getContent(): string;

    public function setContent(string $content): void;

    public function getChildren(): array;

    public function addChild(HtmlInterface $child): void;

    public function setChildren(array $children): void;

}
