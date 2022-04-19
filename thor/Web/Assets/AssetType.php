<?php

namespace Thor\Web\Assets;

use JetBrains\PhpStorm\ArrayShape;

enum AssetType
{

    case STYLESHEET;
    case JAVASCRIPT;

    public function getExtension(): string
    {
        return match ($this) {
            self::STYLESHEET => 'css',
            self::JAVASCRIPT => 'js'
        };
    }

    public function getMimeType(): string
    {
        return match ($this) {
            self::STYLESHEET => 'text/css',
            self::JAVASCRIPT => 'application/javascript'
        };
    }

    #[ArrayShape(['tag' => 'string', 'src' => 'string', 'attrs' => 'array'])]
    public function getHtmlArguments(): array
    {
        return array_combine(['tag', 'src', 'attrs'],
            match ($this) {
                self::STYLESHEET => ['link', 'href', ['rel' => 'stylesheet']],
                self::JAVASCRIPT => ['script', 'src', ['type' => 'application/javascript']]
            }
        );
    }

    public static function fromExtension(string $type): self
    {
        return match (strtolower($type)) {
            'css'   => self::STYLESHEET,
            'js'    => self::JAVASCRIPT,
            default => throw new \InvalidArgumentException("Invalid asset type [$type]")
        };
    }

}
