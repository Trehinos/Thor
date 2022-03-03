<?php

namespace Thor\Web\Assets;

use Thor\Http\Response\Response;
use Thor\Factories\ResponseFactory;

final class Asset
{

    public readonly string $url;

    public function __construct(
        public readonly AssetType $type,
        public readonly string $identifier,
        public readonly string $filePath,
        ?string $url = null,
    ) {
        $this->url = $url ?? $this->filePath;
    }

    public function getContent(): string
    {
        if (!file_exists($this->filePath)) {
            return '';
        }

        return file_get_contents($this->filePath);
    }

    public function getHtml(): string
    {
        return match ($this->type) {
            AssetType::STYLE => "<link rel=\"stylesheet\" href=\"{$this->url}\">",
            AssetType::SCRIPT => "<script src=\"{$this->url}\"></script>",
            default => ''
        };
    }

    public function getResponse(): Response
    {
        $type = match ($this->type) {
            AssetType::STYLE => 'style/css',
            AssetType::SCRIPT => "application/js",
            default => ''
        };

        if (file_exists($this->filePath)) {
            return ResponseFactory::ok($this->getContent(), ['Content-Type' => $type]);
        }

        return ResponseFactory::notFound("[{$this->url}] Asset not found...");
    }

}
