<?php

namespace Thor\Factories;

use Thor\Web\Assets\Asset;
use Thor\Web\Assets\AssetType;
use Thor\Configuration\Configuration;

final class AssetsListFactory
{

    public static function produce(Configuration $assetsConfiguration): array
    {
        $assetsList = [];
        foreach ($assetsConfiguration->getArrayCopy()['lists'] as $name => $data) {
            $assetsList[$name] = new Asset(AssetType::fromType($data['type']), $name, $data['list']);
        }

        return $assetsList;
    }


}
