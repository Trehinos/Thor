<?php

namespace Thor\Factories;

use Thor\Globals;
use Thor\Web\Assets\Asset;
use Thor\Web\Assets\AssetType;
use Thor\Web\Assets\MergedAsset;
use Thor\Configuration\Configuration;
use Thor\Configuration\TwigConfiguration;
use Thor\Configuration\ConfigurationFromFile;

final class AssetsListFactory
{

    /**
     * @param Configuration $assetsConfiguration
     *
     * @return Asset[]
     */
    public static function produce(Configuration $assetsConfiguration): array
    {
        $assetsList = [];
        $twigConfig = TwigConfiguration::get();
        foreach ($assetsConfiguration->getArrayCopy() as $name => $data) {
            if (is_string($data)) {
                $type = explode(".", $data)[1];
                $data = [
                    'type'     => $type,
                    'filename' => Globals::STATIC_DIR . $twigConfig['assets_dir'] . $data,
                ];
                $assetsList[$name] = new Asset(
                    AssetType::fromExtension($data['type']),
                    $name,
                    $data['filename']
                );
            } else {
                $assetsList[$name] = new MergedAsset(
                    AssetType::fromExtension($data['type']),
                    $name,
                    array_map(
                        fn(string $filename) => Globals::STATIC_DIR . $twigConfig['assets_dir'] . $filename,
                        $data['list']
                    )
                );
            }
        }

        return $assetsList;
    }

    /**
     * @return Asset[]
     */
    public static function listFromConfiguration(): array
    {
        return self::produce(ConfigurationFromFile::fromFile('assets/list', true));
    }

}
