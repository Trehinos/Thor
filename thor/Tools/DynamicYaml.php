<?php

namespace Thor\Tools;

use Symfony\Component\Yaml\Yaml;

final class DynamicYaml
{

    private function __construct()
    {
    }

    /**
     * @param string         $filename
     * @param callable|array $context (array): array
     *
     * @return array
     */
    public static function fromFile(string $filename, callable|array $context = []): array
    {
        $data = Yaml::parseFile($filename);
        if (is_callable($context)) {
            $arrContext = $context($data);
        } else {
            $arrContext = $context;
        }
        self::interpolateData($data, $arrContext);

        return $data;
    }

    private static function interpolateData(array &$data, array $context): void
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                self::interpolateData($v, $context);
                $data[$k] = $v;
                continue;
            }
            $data[$k] = Strings::interpolate($v, $context, PlaceholderFormat::BASH_STYLE);
        }
    }

    public static function withAutoContext(string $filename, ?string $key = null): array
    {
        return DynamicYaml::fromFile($filename, fn(array $dataFromFile) => array_map(
            fn(string|int $index, array $element) => $key === null ? $index : $element[$key],
            array_keys($dataFromFile),
            $dataFromFile
        ));
    }

}
