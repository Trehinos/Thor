<?php

namespace Thor\Http\Web\Assets;

use Thor\Common\Stream\Stream;
use Thor\Common\Stream\StreamInterface;
use Thor\Framework\Thor;
use Thor\Http\UriInterface;
use Thor\Http\Web\Node;
use Thor\Http\Web\TextNode;

/**
 *
 */

/**
 *
 */
class Asset extends Node implements AssetInterface
{

    /**
     * @param AssetType                                    $type
     * @param string                                       $name
     * @param string                                       $filename
     * @param UriInterface                                 $uri
     * @param \Thor\Common\Stream\StreamInterface|null $file
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
