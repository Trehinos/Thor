<?php

namespace Thor\Factories;

use Twig\TwigFilter;
use Thor\Http\Server\WebServer;

final class TwigFilterFactory
{

    public static function _(WebServer $server): TwigFilter
    {
        return new TwigFilter(
            '_', // (lang) sprintf ->
            function (string $str, array $arguments = []) use ($server) {
                $foundStr = $server->getLanguage()[$str] ?? null;
                if ($foundStr && !empty($arguments)) {
                    $foundStr = sprintf($foundStr, ...$arguments);
                }
                return $foundStr ?? $str;
            },
            ['is_safe' => ['html'], 'is_variadic' => true]
        );
    }

    public static function classname(): TwigFilter
    {
        return new TwigFilter(
            'classname',
            fn($value) => substr($value, strrpos($value, '\\') + 1)
        );
    }

}
