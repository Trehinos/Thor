<?php

namespace Thor\Message\Part;

use Thor\Message\Headers\Headers;
use Thor\Message\Headers\ContentDisposition;
use Thor\Message\Headers\ContentTransferEncoding;

class TextPart extends DataPart
{

    public function __construct(
        public readonly string $body,
        string $charset = 'utf-8',
        string $mediaSubType = 'plain',
        ContentTransferEncoding $encoding = ContentTransferEncoding::BASE64,
        ?Headers $additionalHeaders = null
    ) {
        parent::__construct('text', $mediaSubType, "charset=\"$charset\"", $encoding, ContentDisposition::INLINE, $additionalHeaders);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public static function plain(
        string $body,
        string $charset = 'utf-8',
        ContentTransferEncoding $encoding = ContentTransferEncoding::BASE64,
        ?Headers $additionalHeaders = null
    ): self {
        return new self($body, $charset, 'plain', $encoding, $additionalHeaders);
    }

}
