<?php

namespace Thor\Web\Assets;

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

}
