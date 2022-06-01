<?php

namespace Thor\Tools;

use Stringable;
use Symfony\Component\Yaml\Yaml;

/**
 *
 */

/**
 *
 */
final class DynamicYaml
{

    private function __construct()
    {
    }

    /**
     * @param string            $filename
     * @param string|null       $key
     * @param callable|null     $selector fn (string $key, array $rowFromFile): array -> element in context
     * @param PlaceholderFormat $format
     *
     * @return array
     */
    public static function withAutoContext(
        string $filename,
        ?string $key = null,
        ?callable $selector = null,
        PlaceholderFormat $format = PlaceholderFormat::SHELL
    ): array {
        return self::fromFile(
            $filename,
            fn(array $dataFromFile) => array_combine(
                $key === null
                    ? array_keys($dataFromFile)
                    : array_map(fn(array $element) => $element[$key] ?? null, $dataFromFile),
                $selector === null
                    ? array_values($dataFromFile)
                    : array_map($selector, array_keys($dataFromFile), $dataFromFile)
            ),
            $format
        );
    }

    /**
     * @param string                                    $filename
     * @param callable|array<string, scalar|Stringable> $context fn (array $dataFromFile): array -> whole context
     * @param PlaceholderFormat                         $format
     *
     * @return array
     */
    public static function fromFile(
        string $filename,
        callable|array $context = [],
        PlaceholderFormat $format = PlaceholderFormat::SHELL
    ): array {
        $data = Yaml::parseFile($filename);
        if (is_callable($context)) {
            $arrContext = $context($data);
        } else {
            $arrContext = $context;
        }
        foreach ($arrContext as $k => $v) {
            if (is_array($v)) {
                $arrContext[$k] = self::interpolateData($v, $arrContext, $format);
            } else {
                $arrContext[$k] = self::interpolate($v, $arrContext, $format);
            }
        }
        return self::interpolateData($data, $arrContext, $format);
    }

    /**
     * @param array             $data
     * @param array             $context
     * @param PlaceholderFormat $format
     *
     * @return array
     */
    private static function interpolateData(array $data, array $context, PlaceholderFormat $format): array
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $v = self::interpolateData($v, $context, $format);
                $data[$k] = $v;
                continue;
            }

            $data[$k] = self::interpolate($v, $context, $format);
        }

        return $data;
    }

    /**
     * @param string            $string
     * @param array             $context
     * @param PlaceholderFormat $placeholder
     *
     * @return string|array
     */
    private static function interpolate(
        string $string,
        array $context,
        PlaceholderFormat $placeholder
    ): string|array {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_array($val) && $placeholder->matches($key, $string)) {
                return $val;
            }
            if (is_scalar($val) || $val instanceof Stringable) {
                $placeholder->setReplace($replace, $key, $val);
            }
        }

        return strtr($string, $replace);
    }

}
