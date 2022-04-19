<?php

namespace Thor\Web\Assets;

use Thor\Thor;
use Thor\Globals;
use Thor\Http\Uri;
use Thor\Web\Node;
use Thor\Stream\Stream;
use Thor\Stream\StreamInterface;
use Thor\Configuration\Configuration;
use Thor\Configuration\TwigConfiguration;

class Asset extends Node implements AssetInterface
{

    private static Configuration $assetsConfiguration;
    private Uri $uri;

    public function __construct(
        public readonly AssetType $type,
        public readonly string $filename,
        protected ?StreamInterface $file = null
    ) {
        $attrs = $this->getType()->getHtmlArguments();
        parent::__construct($attrs['tag']);
        foreach (array_merge($attrs['attrs'], [$attrs['src'] => "{$this->uri}"]) as $attr => $value) {
            $this->setAttribute($attr, $value);
        }

        self::$assetsConfiguration ??= TwigConfiguration::get();
        $this->file ??= Stream::createFromFile("{$this->filename}", "r");
        $this->uri = Uri::fromGlobals()->withPath(self::$assetsConfiguration['assets_cache'] . "/{$filename}");
        if (Thor::isDev()) {
            $this->uri = $this->uri->withQuery(['version' => date('YmdHis')]);
        }
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
