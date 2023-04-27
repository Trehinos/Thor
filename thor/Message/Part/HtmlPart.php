<?php

namespace Thor\Message\Part;

use Thor\Message\Headers\Headers;
use Thor\Message\Headers\ContentDisposition;
use Thor\Message\Headers\ContentTransferEncoding;

class HtmlPart extends TextPart
{

    public function __construct(
        string $body,
        string $charset = 'utf-8',
        ContentTransferEncoding $encoding = ContentTransferEncoding::BASE64,
        ?Headers $additionalHeaders = null
    ) {
        parent::__construct($body, $charset, 'html', $encoding, $additionalHeaders);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public static function embedded(
        string $title,
        string $body,
        string $lang = 'fr',
        string $charset = 'utf-8',
        ContentTransferEncoding $encoding = ContentTransferEncoding::BASE64,
        ?Headers $additionalHeaders = null
    ): self {
        return new self(
            <<<§
            <html lang="$lang">
            <head>
                <meta charset="$charset">
                <title>$title</title>
            </head>
            <body>$body</body>
            </html>
            §, $charset, $encoding, $additionalHeaders
        );
    }

    public static function attached(
        string $filename,
        string $title,
        string $body,
        string $lang = 'fr',
        string $charset = 'utf-8',
        ContentTransferEncoding $encoding = ContentTransferEncoding::BASE64,
        ?Headers $additionalHeaders = null
    ): self {
        $part = new self(
            <<<§
            <html lang="$lang">
            <head>
                <meta charset="$charset">
                <title>$title</title>
            </head>
            <body>$body</body>
            </html>
            §,
            $charset, $encoding, $additionalHeaders
        );
        $part->headers['Content-Disposition'] = ContentDisposition::ATTACHMENT->get(['filename' => $filename]);

        return $part;
    }

}
