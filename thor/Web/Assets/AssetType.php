<?php

namespace Thor\Web\Assets;

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

    public static function fromType(string $type): self
    {
        return match ($type) {
            'css' => self::STYLESHEET,
            'js' => self::JAVASCRIPT,
            default => throw new \InvalidArgumentException("Invalid asset type [$type]")
        };
    }

}