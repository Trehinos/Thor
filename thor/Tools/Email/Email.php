<?php

namespace Thor\Tools\Email;

use Exception;
use Thor\Thor;
use Thor\Tools\Guid;
use Thor\Tools\Strings;

/**
 * A class that helps send email through the PHP mail() function.
 */
class Email extends Part
{

    /** @var Part[] */
    private array $parts;

    public readonly string $boundary;

    /**
     * Constructs a new Email object with specified subject and from email address.
     *
     * @param string $subject
     * @param string $from
     * @param array $additionalHeaders
     * @param string|null $message
     *
     * @throws Exception
     */
    public function __construct(
        private readonly string $subject,
        private readonly string $from,
        array                   $additionalHeaders = [],
        ?string                 $message = null
    ) {
        $this->boundary = Guid::base64();
        $headers = new Headers(
            Strings::interpolate(Headers::TYPE_MULTIPART, ['boundary' => $this->boundary]),
            'binary'
        );
        unset($headers['Content-Disposition']);
        $headers['MIME-Version'] = '1.0';
        $headers['X-Mailer'] = Thor::appName() . ' ' . Thor::versionName() . ' [' . Thor::version() . ']';
        array_walk(
            $additionalHeaders,
            function (string $value, string $name) use ($headers) {
                $headers[$name] = $value;
            }
        );

        parent::__construct($headers, '');

        if ($message !== null) {
            $this->addPart(Part::text($message));
        }
    }

    /**
     * Replaces LF with CRLF.
     *
     * @param string $source
     *
     * @return string
     */
    public static function normalizeEol(string $source): string
    {
        return str_replace("\n", "\r\n", $source);
    }

    /**
     * Adds an email part to this email.
     *
     * @param Part $part
     *
     * @return void
     */
    public function addPart(Part $part): void
    {
        $this->parts[] = $part;
    }

    /**
     * Returns the raw MIME message.
     *
     * @return string
     */
    public function __toString(): string
    {
        return
            "{$this->headers}\r\n\r\n" . $this->getBody();
    }

    /**
     * Returns the raw MIME message's body.
     *
     * @return string
     */
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

    /**
     * Sends the email. Returns false if the mail() instruction fails.
     *
     * @param string|array $to
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
        return mail($to, $this->subject, $this->getBody(), "$this->headers");
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