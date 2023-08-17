<?php

namespace Thor\Framework\Factories;

use Exception;
use Thor\Configuration\ConfigurationFromResource;
use Thor\Thor;
use Thor\Globals;
use Thor\Web\Assets\Asset;
use Thor\Http\UriInterface;
use Thor\Web\Assets\AssetType;
use Thor\FileSystem\FileSystem;
use Thor\Web\Assets\MergedAsset;
use Thor\Web\Assets\CachedAsset;
use Thor\Web\Assets\AssetInterface;
use Thor\Configuration\Configuration;
use Thor\Configuration\ConfigurationFromFile;
use Thor\Framework\Configurations\TwigConfiguration;
use Thor\Framework\Configurations\RoutesConfiguration;

/**
 *
 */

/**
 *
 */
final class AssetsListFactory
{

    /**
     * @param Configuration $assetsDataList
     *
     * @return Asset[]
     *
     * @throws Exception
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
        $twigConfig = TwigConfiguration::get();
        $type ??= is_string($file)
            ? (FileSystem::getExtension($file) ?? throw new Exception("No extension in filename \"$file\""))
            : ($file['type'] ?? throw new Exception('Asset type cannot be inferred from assets data.'));
        $filename = $path . (is_string($file) ? $file : '');
        $list = is_string($file) ? [$file] : $file['list'];
        $cached = is_string($file) ? false : ($file['cache'] ?? false);

        return is_string($file)
            ? new Asset(
                AssetType::fromExtension($type),
                $name,
                $filename,
                $uri
            )
            : ($cached
                ? new CachedAsset(
                    AssetType::fromExtension($type),
                    $name,
                    $uri,
                    array_map(
                        fn(string $filename) => $path . $filename,
                        $list
                    ),
                    !Thor::isDebug() ? 600 : 1,
                    $twigConfig['assets_cache'],
                    Globals::WEB_DIR . $twigConfig['assets_cache']
                )
                : new MergedAsset(
                    AssetType::fromExtension($type),
                    $name,
                    $uri,
                    array_map(
                        fn(string $filename) => $path . $filename,
                        $list
                    )
                ));
    }

    /**
     * @return Asset[]
     * @throws Exception
     * @throws Exception
     */
    public static function listFromConfiguration(): array
    {
        return self::produce(ConfigurationFromResource::fromFile('assets/list', true));
    }

}
