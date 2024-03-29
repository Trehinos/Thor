<?php

namespace Thor\Web\Assets;

use Thor\Thor;
use Thor\Web\Node;
use Thor\Web\TextNode;
use Thor\Stream\Stream;
use Thor\Http\UriInterface;
use Thor\Stream\StreamInterface;

/**
 *
 */

/**
 *
 */
class Asset extends Node implements AssetInterface
{

    /**
     * @param AssetType            $type
     * @param string               $name
     * @param string               $filename
     * @param UriInterface         $uri
     * @param StreamInterface|null $file
     */
    public function __construct(
        public readonly AssetType $type,
        public readonly string $name,
        public readonly string $filename,
        public UriInterface $uri,
        protected ?StreamInterface $file = null,
    ) {
        $this->file ??= Stream::createFromFile("{$this->filename}", "r");
        if (Thor::isDev()) {
            $this->uri = $this->uri->withQuery(['version' => date('YmdHis')]);
        }
        parent::__construct('');
        $this->setNode();
    }

    /**
     * @return void
     */
    public function setNode(): void
    {
        $attrs = $this->getType()->getHtmlArguments();
        $this->setName($attrs['tag']);
        foreach (array_merge($attrs['attrs'], [$attrs['src'] => "{$this->uri}"]) as $attr => $value) {
            $this->setAttribute($attr, $value);
        }
        if ($this->type === AssetType::JAVASCRIPT) {
            $this->addChild(new TextNode('', $this));
        }
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $this->file->rewind();
        return $this->file->getContents();
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return AssetType
     */
    public function getType(): AssetType
    {
        return $this->type;
    }

}
