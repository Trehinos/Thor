<?php

namespace Thor\Message;

use Thor\Message\Part\FilePart;
use Thor\Message\Part\HtmlPart;
use Thor\Message\Part\Multipart;
use Thor\Message\Headers\Headers;
use Thor\Message\Headers\ContentDisposition;

/**
 * @see RFC2045 - Headers & Parts
 * @see RFC2046 - MIME Media
 * @see RFC2047 - UTF-8 Headers
 */
class Email extends Message
{
    public function __construct(
        string $from,
        string $subject,
        public readonly string $body,
        public readonly array $images = [],
        public readonly array $files = [],
        public readonly string $charset = 'utf-8',
        public readonly string $lang = 'fr',
        Headers $headers = new Headers()
    ) {
        parent::__construct($from, $subject, [
            Multipart::related(
                [
                    HtmlPart::embedded($subject, $this->body, $this->lang, $this->charset),
                    ...array_map(
                        fn(string $cid, string $fileName) => FilePart::inlineImage($fileName, $cid),
                        array_keys($this->images),
                        $this->images
                    ),
                ]
            ),
            ...array_map(
                fn(string $filename) => new FilePart($filename, disposition: ContentDisposition::ATTACHMENT),
                $this->files
            ),
        ], headers:         $headers);
    }

}
