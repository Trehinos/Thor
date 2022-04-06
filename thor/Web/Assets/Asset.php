<?php

namespace Thor\Web\Assets;

use Thor\Thor;
use Thor\Globals;
use Thor\Http\Uri;
use Thor\Configuration\Configuration;
use Thor\Configuration\ConfigurationFromFile;

final class Asset
{

    private static Configuration $assetsConfiguration;
    private string $path;
    private Uri $uri;

    public function __construct(
        private readonly AssetType $type,
        private readonly string $name,
        private readonly array $fileList
    ) {
        self::$assetsConfiguration ??= ConfigurationFromFile::fromFile('assets/assets', true);
        $filename = "{$this->name}.{$this->type->getExtension()}";
        $this->path = Globals::WEB_DIR . self::$assetsConfiguration['cache-dir'] . "/{$filename}";
        $this->uri = Uri::fromGlobals()->withPath(self::$assetsConfiguration['cache-dir'] . "/{$filename}");
        if (Thor::isDev()) {
            $this->uri = $this->uri->withQuery(['v' => date('YmdHis')]);
        }
    }

    public function getFiles(): array
    {
        return $this->fileList;
    }

    public function merge(): string
    {
        return array_reduce(
            $this->fileList,
            function (?string $mergedAssets, string $filename) {
                return ($mergedAssets ?? '') . file_get_contents(Globals::STATIC_DIR . 'assets/' . $filename);
            }
        );
    }

    public function cache(): void
    {
        if (file_exists($this->path) && !Thor::isDev()) {
            return;
        }
        file_put_contents($this->path, $this->merge());
    }

    public function getHtml(): string
    {
        return match ($this->type) {
            AssetType::JAVASCRIPT => "<script src=\"{$this->uri}\"></script>",
            AssetType::STYLESHEET => "<link rel=\"stylesheet\" href=\"{$this->uri}\">"
        };
    }

}
