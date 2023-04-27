<?php

namespace Thor\Message;

use Thor\Thor;
use Thor\Message\Part\FilePart;
use Thor\Message\Part\Multipart;
use Thor\Message\Headers\Headers;
use Thor\Message\Headers\ContentTransferEncoding;

class Message extends Multipart
{
    public function __construct(
        public readonly string $from,
        public readonly string $subject,
        public array $parts = [],
        string $mediaSubType = Multipart::MIXED,
        ContentTransferEncoding $encoding = ContentTransferEncoding::BIT7,
        Headers $headers = new Headers(),
    ) {
        $headers['MIME-Version'] = '1.0';
        parent::__construct($this->parts, $mediaSubType, encoding: $encoding, additionalHeaders: $headers);
        $this->headers['X-Mailer'] = Thor::appName() . ' [' . Thor::version() . ']';
    }

    public function addFile(string $filename): void
    {
        $this->parts[] = new FilePart($filename);
    }

    /**
     * Sends the email. Returns false if the mail() instruction fails.
     *
     * @param string|array      $to
     * @param string|array|null $cc
     * @param string|array|null $bcc
     *
     * @return bool
     */
    public function send(string|array $to, string|array|null $cc = null, string|array|null $bcc = null): bool
    {
        if (is_array($to)) {
            $to = implode(', ', $to);
        }
        $this->extractField('Cc', $cc);
        $this->extractField('Bcc', $bcc);
        $this->headers['From'] = $this->from;
        return mail($to, Headers::encode($this->subject), $this->getBody(), "$this->headers");
    }

    private function extractField(string $fieldName, string|array|null $field): void
    {
        if ($field !== null) {
            if (is_array($field)) {
                $field = implode(', ', $field);
            }
            $this->headers[$fieldName] = $field;
        }
    }

}
