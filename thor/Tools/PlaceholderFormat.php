<?php

namespace Thor\Tools;

/**
 * * SIGIL
 *   Format : "Your name is $name."
 *
 * * CURLY
 * Format : "Your name is {name}."
 *
 * * SHELL
 * Format : "Your name is ${name}."
 *
 * * BRACKETS
 * Format : "Your name is [=name]."
 *
 * * ESCAPE
 * Format : "Your name is \\(name)."
 */
enum PlaceholderFormat
{

    case SIGIL;
    case CURLY;
    case SHELL;
    case BRACKETS;
    case ESCAPE;

    public function format(string $key): string
    {
        return match($this) {
            self::SIGIL    => "\$$key",
            self::CURLY    => '{' . "$key}",
            self::SHELL    => "\${" . "$key}",
            self::BRACKETS => "[=$key]",
            self::ESCAPE   => "\\($key)",
        };
    }

    public function setReplace(array &$replaces, string $key, mixed $value): void
    {
        $replaces[match ($this) {
            self::SIGIL    => "\$$key",
            self::CURLY    => '{' . "$key}",
            self::SHELL    => "\${" . "$key}",
            self::BRACKETS => "[=$key]",
            self::ESCAPE   => "\\($key)",
        }] = $value;
    }

    public function matches(string $key, string $str): bool
    {
        return $this->format($key) === $str;
    }

}
