<?php

namespace Thor\Tools;

/**
 *
 */

/**
 *
 */
class Email
{

    public const EMAIL_TEXT = 1;
    public const EMAIL_MULTIPART = 2;
    public const EMAIL_HTML = 3;
    public const EMAIL_OCTET_STREAM = 4;

    public const UNIX_EOL = "\n";
    public const MAIL_EOL = "\r\n";

    public const DEFAULT_HEADERS = [
        self::EMAIL_TEXT         => [
            'Content-Type' => 'text/plain; charset=utf-8',
        ],
        self::EMAIL_MULTIPART    => [
            'MIME-Version'              => '1.0',
            'Content-Type'              => 'multipart/mixed; boundary="{boundary}"',
            'Content-Transfer-Encoding' => '7bit',
        ],
        self::EMAIL_HTML         => [
            'Content-Type' => 'text/html; charset=utf-8',
        ],
        self::EMAIL_OCTET_STREAM => [
            'Content-Type' => 'application/octet-stream; name="{name}"',
        ],
    ];

    private array $to = [];
    private array $files = [];
    private string $boundary;

    /**
     * @param string $from
     * @param string $subject
     * @param string $rawMessage
     * @param array  $context
     * @param array  $headers
     * @param int    $type
     *
     * @throws \Exception
     */
    public function __construct(
        private string $from,
        private string $subject,
        private string $rawMessage,
        private array $context = [],
        private array $headers = [],
        private int $type = self::EMAIL_TEXT
    ) {
        $this->boundary = bin2hex(random_bytes(16));
        $this->headers = [...$this->headers, ...self::DEFAULT_HEADERS[$type]];
    }

    /**
     * @param string      $filepath
     * @param string|null $filename
     *
     * @return array
     */
    public function file(string $filepath, ?string $filename = null): array
    {
        $this->files[$filename ?? basename($filepath)] = $filepath;

        return $this->files;
    }

    /**
     * @param string|null $from
     *
     * @return string
     */
    public function from(?string $from = null): string
    {
        $this->from = $from ?? $this->from;

        return $this->from;
    }

    /**
     * @param string|null $subject
     *
     * @return string
     */
    public function subject(?string $subject = null): string
    {
        $this->subject = $subject ?? $this->subject;

        return $this->subject;
    }

    /**
     * @param string|null $rawMessage
     *
     * @return string
     */
    public function rawMessage(?string $rawMessage = null): string
    {
        $this->rawMessage = $rawMessage ?? $this->rawMessage;

        return $this->rawMessage;
    }

    /**
     * @param string $varName
     * @param mixed  $value
     *
     * @return $this
     */
    public function set(string $varName, mixed $value = true): static
    {
        $this->context[$varName] = $value;

        return $this;
    }

    /**
     * @param string|null $header
     * @param string|null $value
     *
     * @return array
     */
    public function header(?string $header = null, ?string $value = null): array
    {
        if ($header !== null) {
            $this->headers[$header] = $value ?? true;
        }

        return $this->headers;
    }

    /**
     * @param string|null $email
     *
     * @return array
     */
    public function to(?string $email): array
    {
        $this->to[] = $email;

        return $this->to;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        if ($this->type !== self::EMAIL_MULTIPART) {
            return Strings::interpolate(self::normalize($this->rawMessage), $this->context);
        }
        $this->headers['Content-Type'] = Strings::interpolate(
            $this->headers['Content-Type'],
            ['boundary' => $this->boundary]
        );
        $parts = [$this->addPart(self::DEFAULT_HEADERS[self::EMAIL_HTML], $this->rawMessage)];
        foreach ($this->files as $file => $path) {
            $parts[] = $this->addPart(
                [
                    'Content-Type'              => Strings::interpolate(
                        self::DEFAULT_HEADERS[self::EMAIL_OCTET_STREAM]['Content-Type'],
                        ['name' => $file]
                    ),
                    'Content-Disposition'       => "attachment; filename=\"$file\"",
                ],
                file_get_contents($path)
            );
        }
        return implode($parts);
    }

    /**
     * @param string $message
     *
     * @return string
     */
    public static function normalize(string $message): string
    {
        return str_contains($message, self::MAIL_EOL)
            ? $message
            : str_replace(self::UNIX_EOL, self::MAIL_EOL, $message);
    }

    /**
     * @return bool
     */
    public function send(): bool
    {
        if (empty($this->to)) {
            return false;
        }

        $message = $this->getMessage()."--{$this->boundary}--";
        dump($message);
        $headers = $this->headers + self::DEFAULT_HEADERS[$this->type];

        return mail(
            implode(',', $this->to),
            $this->subject,
            $message,
            $headers
        );
    }

    /**
     * @param array  $headers
     * @param string $body
     *
     * @return string
     */
    public function addPart(array $headers, string $body): string
    {
        if ($this->type !== self::EMAIL_MULTIPART) {
            return '';
        }
        $headers = implode(
            self::UNIX_EOL,
            array_merge(
                array_map(
                    fn(string $headerName, string $headerValue) => "$headerName: $headerValue",
                    array_keys($headers),
                    array_values($headers)
                ),
                ['Content-Transfer-Encoding: base64']
            )
        );

        $body = chunk_split(base64_encode($body), separator: "\n");

        return self::normalize(
            <<<§
            --{$this->boundary}
            $headers
            
            $body
            
            §
        );
    }

}
