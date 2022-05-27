<?php

namespace Thor\Tools;

use Symfony\Component\Yaml\Yaml;

final class DynamicYaml
{

    private function __construct()
    {
    }

    /**
     * @param string            $filename
     * @param callable|array    $context fn (array): array
     * @param PlaceholderFormat $format
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
        self::interpolateData($data, $arrContext, $format);

        return $data;
    }

    private static function interpolateData(array &$data, array $context, PlaceholderFormat $format): void
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                self::interpolateData($v, $context, $format);
                $data[$k] = $v;
                continue;
            }
            $data[$k] = Strings::interpolate($v, $context, $format);
        }
    }

    public static function withAutoContext(string $filename, ?string $key = null,  ?callable $selector = null): array
    {
        return self::fromFile(
            $filename,
            fn(array $dataFromFile) => array_combine(
                $key === null
                    ? array_keys($dataFromFile)
                    : array_map(fn(array $element) => $element[$key], $dataFromFile),
                $selector === null
                    ? array_values($dataFromFile)
                    : array_map($selector, $dataFromFile)
            )
        );
    }

}
