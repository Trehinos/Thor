<?php

namespace Thor\Factories;

use Thor\Web\Assets\Asset;
use Thor\Web\Assets\AssetType;
use Thor\Configuration\Configuration;
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
        foreach ($assetsConfiguration->getArrayCopy()['lists'] as $name => $data) {
            $assetsList[$name] = new Asset(AssetType::fromType($data['type']), $name, $data['list']);
        }

        return $assetsList;
    }

    /**
     * @return Asset[]
     */
    public static function listFromConfiguration(): array
    {
        return self::produce(ConfigurationFromFile::fromFile('assets/assets.yml'));
    }

}
