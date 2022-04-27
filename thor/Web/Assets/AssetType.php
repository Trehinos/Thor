<?php

namespace Thor\Web\Assets;

use JetBrains\PhpStorm\ArrayShape;

enum AssetType
{

    case STYLESHEET;
    case JAVASCRIPT;
    case IMAGE_PNG;
    case IMAGE_GIF;
    case IMAGE_BMP;
    case IMAGE_JPEG;

    public function getExtension(): string
    {
        return match ($this) {
            self::STYLESHEET => 'css',
            self::JAVASCRIPT => 'js',
            self::IMAGE_PNG => 'png',
            self::IMAGE_GIF => 'gif',
            self::IMAGE_BMP => 'bmp',
            self::IMAGE_JPEG => 'jpg',
        };
    }

    public function getMimeType(): string
    {
        return match ($this) {
            self::STYLESHEET => 'text/css',
            self::JAVASCRIPT => 'application/javascript',
            self::IMAGE_PNG => 'image/png',
            self::IMAGE_GIF => 'image/gif',
            self::IMAGE_BMP => 'image/bmp',
            self::IMAGE_JPEG => 'image/jpeg',
        };
    }

    #[ArrayShape(['tag' => 'string', 'src' => 'string', 'attrs' => 'array'])]
    public function getHtmlArguments(): array
    {
        return array_combine(['tag', 'src', 'attrs'],
            match ($this) {
                self::STYLESHEET => ['link', 'href', ['rel' => 'stylesheet']],
                self::JAVASCRIPT => ['script', 'src', ['type' => 'application/javascript']],
                self::IMAGE_PNG, self::IMAGE_GIF, self::IMAGE_BMP, self::IMAGE_JPEG => ['img', 'src', []],
            }
        );
    }

    public static function fromExtension(string $type): self
    {
        return match (strtolower($type)) {
            'css' => self::STYLESHEET,
            'js' => self::JAVASCRIPT,
            'png' => self::IMAGE_PNG,
            'gif' => self::IMAGE_GIF,
            'bmp' => self::IMAGE_BMP,
            'jpg', 'jpeg' => self::IMAGE_JPEG,
            default => throw new \InvalidArgumentException("Invalid asset type [$type]")
        };
    }

}
