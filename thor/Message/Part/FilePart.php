<?php

namespace Thor\Message\Part;

use Thor\Tools\Guid;
use Thor\FileSystem\FileSystem;
use Thor\Message\Headers\Headers;
use Thor\Message\Headers\ContentDisposition;
use Thor\Message\Headers\ContentTransferEncoding;

class FilePart extends DataPart
{

    public function __construct(
        public readonly string $filename,
        string $mediaType = 'application',
        string $mediaSubType = 'octet-stream',
        public ?string $contentId = null,
        ContentTransferEncoding $encoding = ContentTransferEncoding::BASE64,
        ContentDisposition $disposition = ContentDisposition::INLINE,
        ?Headers $additionalHeaders = null,
    ) {
        $basename = basename($this->filename);
        parent::__construct(
            $mediaType,
            $mediaSubType,
            "name=\"$basename\"",
            $encoding,
            $disposition,
            $additionalHeaders
        );
        $this->contentId ??= Guid::hex();
        $this->headers['Content-Disposition'] = $disposition->get(['filename' => $basename]);
        $this->headers['Content-ID'] = "<{$this->contentId}>";
        $this->headers['Content-Location'] = $basename;
    }

    public function getBody(): string
    {
        return file_get_contents($this->filename);
    }

    public static function inlineImage(
        string $filename,
        ContentDisposition $disposition = ContentDisposition::INLINE
    ): self {
        $basename = basename($filename);
        return new self(
            $filename,
            'image',
            FileSystem::getExtension($basename),
            encoding: ContentTransferEncoding::BASE64,
            disposition: $disposition
        );
    }

}
