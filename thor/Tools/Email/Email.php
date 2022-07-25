<?php

namespace Thor\Tools\Email;

use Thor\Tools\Guid;
use Thor\Tools\Strings;

class Email extends Part
{

    /** @var Part[] */
    private array $parts;

    public readonly string $boundary;

    public function __construct(
        private string $subject,
        private string $from,
        array $additionnalHeaders = [],
        ?string $message = null
    ) {
        $this->boundary = Guid::base64();
        $headers = new Headers(
            Strings::interpolate(Headers::TYPE_MULTIPART, ['boundary' => $this->boundary]),
            'binary'
        );
        unset($headers['Content-Disposition']);
        array_walk(
            $additionnalHeaders,
            function (string $value, string $name) use ($headers) {
                $headers[$name] = $value;
            }
        );

        parent::__construct($headers, '');

        if ($message !== null) {
            $this->addPart(Part::text($message));
        }
    }

    public function addPart(Part $part)
    {
        $this->parts[] = $part;
    }

    public function __toString(): string
    {
        return
            "{$this->headers}\r\n\r\n" . $this->getBody();
    }

    public function getBody(): string
    {
        return "\r\n\r\n--$this->boundary\r\n" .
               implode(
                   "\r\n\r\n--$this->boundary\r\n",
                   array_map(
                       fn(Part $part) => "$part",
                       $this->parts
                   )
               ) .
               "\r\n\r\n--$this->boundary--\r\n";
    }

    public function send(string|array $to, string|array|null $cc = null, string|array|null $bcc = null): bool
    {
        if (is_array($to)) {
            $to = implode(', ', $to);
        }
        if ($cc !== null) {
            if (is_array($cc)) {
                $cc = implode(', ', $cc);
            }
            $this->headers['Cc'] = $cc;
        }
        if ($bcc !== null) {
            if (is_array($bcc)) {
                $bcc = implode(', ', $bcc);
            }
            $this->headers['Bcc'] = $bcc;
        }
        $this->headers['From'] = $this->from;
        return mail($to, $this->subject, $this->getBody(), "$this->headers");
    }

}