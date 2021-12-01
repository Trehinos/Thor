<?php

namespace Thor\Html;

/**
 * Describes and Html node/tag.
 *
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface HtmlInterface
{

    /**
     * Returns the valid Html corresponding the object.
     *
     * @return string
     */
    public function toHtml(): string;

    /**
     * Gets one HTML attribute from its name. Returns null if not found.
     *
     * @param string $name
     *
     * @return string|bool|null
     */
    public function getAttr(string $name): string|bool|null;

    /**
     * Sets one HTML attribute from its name. Set to null to remove it.
     *
     * @param string           $name
     * @param string|bool|null $value
     *
     * @return void
     */
    public function setAttr(string $name, string|bool|null $value): void;

    /**
     * Gets the text content.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Sets the text content (empties the children list).
     *
     * @param string $content
     *
     * @return void
     */
    public function setContent(string $content): void;

    /**
     * Gets all children nodes.
     *
     * @return HtmlInterface[]
     */
    public function getChildren(): array;

    /**
     * Add a child node.
     *
     * @param HtmlInterface $child
     *
     * @return void
     */
    public function addChild(HtmlInterface $child): void;

    /**
     * Sets all children of this element.
     *
     * @param HtmlInterface[] $children
     *
     * @return void
     */
    public function setChildren(array $children): void;

}
