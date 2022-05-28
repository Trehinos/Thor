<?php

namespace Thor\Tools;

/**
 * * `SIGIL` : "Your name is $name."
 *
 * * `CURLY` : "Your name is {name}."
 *
 * * `SHELL` : "Your name is ${name}."
 *
 * * `BRACKETS` : "Your name is [=name]."
 *
 * * `ESCAPE` : "Your name is \\(name)."
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
        $replaces[$this->format($key)] = $value;
    }

    public function matches(string $key, string $str): bool
    {
        return $this->format($key) === $str;
    }

}
