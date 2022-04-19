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
        foreach ($assetsConfiguration->getArrayCopy() as $name => $data) {
            if (is_string($data)) {
                $type = explode(".", $data)[1];
                $data = [
                    'type' => $type,
                    'filename' => $data
                ];
            }
            $assetsList[$name] = new Asset(AssetType::fromExtension($data['type']), $name, $data['list']);
        }

        return $assetsList;
    }

    /**
     * @return Asset[]
     */
    public static function listFromConfiguration(): array
    {
        return self::produce(ConfigurationFromFile::fromFile('assets/list.yml'));
    }

}
