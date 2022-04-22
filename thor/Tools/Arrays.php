<?php

namespace Thor\Tools;

final class Arrays
{

    private function __construct()
    {
    }

    /**
     * Transform an array
     * [
     *      0 => ['KEY' => 'VALUE', 'KEY2' => '', ...],
     *      1 => ['KEY' => 'VALUE', 'KEY2' => '', ...],
     *      2 => ['KEY' => 'VALUE', 'KEY2' => '', ...],
     *      3 => ['KEY' => 'VALUE', 'KEY2' => '', ...],
     *      ...
     * ]
     * into an array
     * [
     *      'KEY' => [0 => 'VALUE', 1 => '', 2 => '', 3 => ''...],
     *      'KEY2' => [0 => 'VALUE', 1 => '', 2 => '', 3 => ''...],
     *      ...
     * ]
     *
     * @param array $input
     *
     * @return array empty array if $input is invalid.
     */
    public static function turnOver(array $input): array
    {
        if (!is_array($input[0] ?? null)) {
            return [];
        }

        return array_combine(
            array_keys($input[0]),
            array_map(
                fn(string $key) => array_column($input, $key),
                array_keys($input[0])
            )
        );
    }
}
