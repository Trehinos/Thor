<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html;

use Exception;

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
     * @param string      $tag
     * @param bool        $autoClose
     * @param array       $attrs
     * @param string|null $id
     */
    public function __construct(string $tag, private bool $autoClose = true, array $attrs = [], ?string $id = null)
    {
        $this->tag = strtolower($tag);
        try {
            $this->attrs = $attrs + [
                    'id' => $id ?? ("{$tag}_" . bin2hex(random_bytes(4))),
                ];
        } catch (Exception) {
            if ($id !== null) {
                $this->attrs = $attrs + ['id' => $id];
            } else {
                $this->attrs = $attrs;
            }
        }
    }

    public static function div(array $attrs = [], array $children = []): HtmlTag
    {
        return self::tag('div', $attrs, $children);
    }

    public static function tag(string $tag, array $attrs = [], array $children = []): HtmlTag
    {
        $div = new self($tag, false, $attrs);
        foreach ($children as $child) {
            $div->addChild($child);
        }
        return $div;
    }

    public function addChild(HtmlInterface $child): void
    {
        if ($this->autoClose) {
            return;
        }

        $this->textContent = null;
        $this->children[] = $child;
    }

    public static function button(string $content, string $onclick = '', array $attrs = []): HtmlTag
    {
        $button = self::tag('button', ['onclick' => $onclick] + $attrs);
        $button->setContent($content);
        return $button;
    }

    public function setContent(string $content): void
    {
        if ($this->autoClose) {
            return;
        }

        $this->children = [];
        $this->textContent = $content;
    }

    public static function icon(string $icon, string $collection = 'fas', bool $fixedWidth = false): string
    {
        $fw = $fixedWidth ? 'fa-fw' : '';
        return "<i class='$collection fa-$icon $fw'></i>";
    }

    public function regenerateID(string $prefix = ''): void
    {
        $this->attrs['id'] = "$prefix{$this->tag}_" .  bin2hex(random_bytes(4));
    }

    public function setAttr(string $name, $value): void
    {
        $this->attrs[$name] = $value;
    }

    public function getAttr(string $name): mixed
    {
        return $this->attrs[$name] ?? null;
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

    public function getContent(): string
    {
        if ($this->autoClose) {
            return '';
        }

        return $this->textContent ?? implode(
                array_map(
                    fn(HtmlTag $child) => $child->toHtml(),
                    $this->getChildren()
                )
            );
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param HtmlInterface[] $children
     */
    public function setChildren(array $children): void
    {
        if ($this->autoClose) {
            return;
        }

        $this->textContent = null;
        $this->children = $children;
    }

}
