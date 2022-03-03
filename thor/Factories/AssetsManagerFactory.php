<?php

namespace Thor\Factories;

use Thor\Web\Assets\AssetsList;
use Thor\Web\Assets\AssetsManager;
use Thor\Configuration\Configuration;

final class AssetsManagerFactory
{

    public static function produce(Configuration $assetsConfiguration): AssetsManager
    {
        $assetsManager = new AssetsManager(
            array_map(
                fn(string $name, array $lists) => (new AssetsList())->addAsset(),
                array_keys($assetsConfiguration),
                array_values($assetsConfiguration)
            ),
            $assetsConfiguration
        );
    }

    public static function addAssetToList(AssetsList $list, array $assets): AssetsList
    {
        if (!empty($assets)) {
            foreach ($assets as $type => $assetsList) {
//
            }
        }

        return $list;
    }

}
