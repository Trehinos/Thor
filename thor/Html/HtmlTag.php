<?php

namespace Thor\Html;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

class HtmlTag implements HtmlInterface
{

    private string $tag;
    private array $attrs;

    /**
     * @var HtmlInterface[]
     */
    private array $children = [];
    private ?string $textContent = null;

    /**
     * FormType constructor.
     *
     * @param string $tag
     * @param bool $autoClose
     * @param array $attrs
     *
     * @throws Exception
     */
    public function __construct(string $tag, private bool $autoClose = true, array $attrs = [])
    {
        $this->tag = strtolower($tag);
        $this->attrs = $attrs + [
                'id' => bin2hex(random_bytes(8)),
                'class' => 'form-control'
            ];
    }

    public function setAttr(string $name, $value): void
    {
        $this->attrs[$name] = $value;
    }

    public function getAttr(string $name): mixed
    {
        return $this->attrs[$name] ?? null;
    }

    private function htmlAttrs(): string
    {
        return implode(
            ' ',
            array_map(
                fn(string $key, $value) => (is_string($value))
                    ? "$key=\"$value\""
                    : (true === $value
                        ? $key
                        : ''
                    )
                ,
                array_keys($this->attrs),
                array_values($this->attrs)
            )
        );
    }

    public function toHtml(): string
    {
        $closeTag = ($this->autoClose && $this->tag !== '') ? '' : "</{$this->tag}>";
        $attrs = empty($this->attrs) ? '' : " {$this->htmlAttrs()}";
        $openTag = '';
        if ($this->tag !== '') {
            $openTag = "<{$this->tag}{$attrs}>";
        }

        $content = $this->getContent();

        return "{$openTag}{$content}{$closeTag}";
    }

    public function getContent(): string
    {
        return $this->textContent ?? implode($this->getChildren());
    }

    public function setContent(string $content): void
    {
        $this->children = [];
        $this->textContent = $content;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(HtmlInterface $child): void
    {
        $this->textContent = null;
        $this->children[] = $child;
    }

    /**
     * @param HtmlInterface[] $children
     */
    public function setChildren(array $children): void
    {
        $this->textContent = null;
        $this->children = $children;
    }
}
