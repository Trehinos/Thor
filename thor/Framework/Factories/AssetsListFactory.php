<?php

namespace Thor\Framework\Factories;

use Thor\Globals;
use Thor\Web\Assets\Asset;
use Thor\Web\Assets\AssetType;
use Thor\Web\Assets\MergedAsset;
use Thor\Configuration\Configuration;
use Thor\Configuration\TwigConfiguration;
use Thor\Configuration\RoutesConfiguration;
use Thor\Configuration\ConfigurationFromFile;

final class AssetsListFactory
{

    /**
     * @param Configuration $assetsDataList
     *
     * @return Asset[]
     */
    public static function produce(Configuration $assetsDataList): array
    {
        $router = RouterFactory::createRouterFromConfiguration(RoutesConfiguration::get('web'));
        $assetsList = [];
        $twigConfig = TwigConfiguration::get();
        foreach ($assetsDataList->getArrayCopy() as $name => $data) {
            $uri = $router->getUrl($twigConfig['assets_route'], ['asset' => $name]);
            if (is_string($data)) {
                $type = explode(".", $data)[1];
                $data = [
                    'type'     => $type,
                    'filename' => Globals::STATIC_DIR . $twigConfig['assets_dir'] . $data,
                ];
                $assetsList[$name] = new Asset(
                    AssetType::fromExtension($data['type']),
                    $name,
                    $data['filename'],
                    $uri
                );
            } else {
                $assetsList[$name] = new MergedAsset(
                    AssetType::fromExtension($data['type']),
                    $name,
                    $uri,
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
