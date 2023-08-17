<?php

namespace Thor\Message\Part;

use Thor\Message\Headers\Headers;
use Thor\Message\Headers\ContentDisposition;
use Thor\Message\Headers\ContentTransferEncoding;

abstract class DataPart extends Part
{
    public function __construct(
        string $mediaType = 'text',
        string $mediaSubType = 'plain',
        array|string $mediaParameter = "charset=utf-8",
        ContentTransferEncoding $encoding = ContentTransferEncoding::BASE64,
        public readonly ContentDisposition $disposition = ContentDisposition::INLINE,
        ?Headers $additionalHeaders = null
    ) {
        parent::__construct($mediaType, $mediaSubType, $mediaParameter, $encoding, $additionalHeaders);
        $this->headers['Content-Disposition'] = $this->disposition->value;
    }
}
