<?php

namespace Thor\Framework\Factories;

use Exception;
use Thor\Globals;
use Thor\Web\Assets\Asset;
use Thor\Http\UriInterface;
use Thor\Web\Assets\AssetType;
use Thor\FileSystem\FileSystem;
use Thor\Web\Assets\MergedAsset;
use Thor\Web\Assets\AssetInterface;
use Thor\Configuration\Configuration;
use Thor\Configuration\ConfigurationFromFile;
use Thor\Framework\Configuration\TwigConfiguration;
use Thor\Framework\Configuration\RoutesConfiguration;

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
        $twigConfig = TwigConfiguration::get();
        $assetsList = [];
        foreach ($assetsDataList->getArrayCopy() as $name => $data) {
            $uri = $router->getUrl($twigConfig['assets_route'], ['asset' => $name]);
            $assetsList[$name] = self::getAsset(
                Globals::STATIC_DIR . $twigConfig['assets_dir'],
                $uri,
                $name,
                $data,
            );
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
     * @throws Exception
     */
    private static function getAsset(
        string $path,
        UriInterface $uri,
        string $name,
        string|array $file,
        ?string $type = null
    ): AssetInterface {
        $type ??= is_string($file)
            ? (FileSystem::getExtension($file) ?? throw new Exception("No extension in filename \"$file\""))
            : ($file['type'] ?? throw new Exception('Asset type cannot be inferred from assets data.'));
        $filename = $path . (is_string($file) ? $file : '');
        $list = is_string($file) ? [$file] : $file['list'];

        return is_string($file)
            ? new Asset(
                AssetType::fromExtension($type),
                $name,
                $filename,
                $uri
            )
            : new MergedAsset(
                AssetType::fromExtension($type),
                $name,
                $uri,
                array_map(
                    fn(string $filename) => $path . $filename,
                    $list
                )
            );
    }

    /**
     * @return Asset[]
     */
    public static function listFromConfiguration(): array
    {
        return self::produce(ConfigurationFromFile::fromFile('assets/list', true));
    }

}
