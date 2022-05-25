<?php

namespace Thor\Tools;

/**
 * ## SIGIL
 * Format : "Your name is $name."
 *
 * ## BRACES
 * Format : "Your name is {name}."
 *
 * ## BASH_STYLE
 * Format : "Your name is ${name}."
 *
 * ## BRACKETS
 * Format : "Your name is [=name]."
 *
 * ## ESCAPE
 * Format : "Your name is \(name)."
 */
enum PlaceholderFormat
{

    case SIGIL;
    case BRACES;
    case BASH_STYLE;
    case BRACKETS;
    case ESCAPE;

    public function replace(array &$replaces, string $key, mixed $value): void
    {
        $replaces[match ($this) {
            self::SIGIL => "\$$key",
            self::BRACES => '{' . "$key}",
            self::BASH_STYLE => "\${" . "$key}",
            self::BRACKETS => "[=$key]",
            self::ESCAPE => "\\($key)",
        }] = $value;
    }

}
