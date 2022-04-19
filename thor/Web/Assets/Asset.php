<?php

namespace Thor\Web\Assets;

use Thor\Thor;
use Thor\Globals;
use Thor\Http\Uri;
use Thor\Web\Node;
use Thor\Web\TextNode;
use Thor\Stream\Stream;
use Thor\Stream\StreamInterface;
use Thor\Factories\RouterFactory;
use Thor\Configuration\Configuration;
use Thor\Configuration\TwigConfiguration;
use Thor\Configuration\RoutesConfiguration;

class Asset extends Node implements AssetInterface
{

    private static Configuration $assetsConfiguration;

    public function __construct(
        public readonly AssetType $type,
        public readonly string $name,
        public readonly string $filename,
        protected ?StreamInterface $file = null
    ) {

        self::$assetsConfiguration ??= TwigConfiguration::get();
        $this->file ??= Stream::createFromFile("{$this->filename}", "r");
        $router = RouterFactory::createRouterFromConfiguration(RoutesConfiguration::get('web'));
        $this->uri = $router->getUrl(self::$assetsConfiguration['assets_route'], ['asset' => $this->name]);
        if (Thor::isDev()) {
            $this->uri = $this->uri->withQuery(['version' => date('YmdHis')]);
        }
        $attrs = $this->getType()->getHtmlArguments();
        parent::__construct($attrs['tag']);
        foreach (array_merge($attrs['attrs'], [$attrs['src'] => "{$this->uri}"]) as $attr => $value) {
            $this->setAttribute($attr, $value);
        }
        if ($this->type === AssetType::JAVASCRIPT) {
            $this->addChild(new TextNode('', $this));
        }
    }

    public function getCachePath(string $basePath = ''): string
    {
        return Globals::WEB_DIR . $basePath . "{$this->filename}.{$this->type->getExtension()}";
    }

    public function getContent(): string
    {
        $this->file->rewind();
        return $this->file->getContents();
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getType(): AssetType
    {
        return $this->type;
    }

}
