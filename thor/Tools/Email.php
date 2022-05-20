<?php

namespace Thor\Tools;

class Email
{

    public const EMAIL_TEXT = 1;
    public const EMAIL_MULTIPART = 2;
    public const EMAIL_HTML = 3;
    public const EMAIL_OCTET_STREAM = 4;

    public const UNIX_EOL = "\n";
    public const MAIL_EOL = "\r\n";

    private const DEFAULT_HEADERS = [
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

    public function __construct(
        private string $from,
        private string $subject,
        private string $rawMessage,
        private array $context = [],
        private array $headers = [],
        private int $type = self::EMAIL_TEXT
    ) {
        $this->boundary = bin2hex(random_bytes(16));
    }

    public function file(string $filepath, ?string $filename = null,): array
    {
        $this->files[$filename ?? basename($filepath)] = $filepath;

        return $this->files;
    }

    public function from(?string $from = null): string
    {
        $this->from = $from ?? $this->from;

        return $this->from;
    }

    public function subject(?string $subject = null): string
    {
        $this->subject = $subject ?? $this->subject;

        return $this->subject;
    }

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

    public function header(?string $header = null, ?string $value = null): array
    {
        if ($header !== null) {
            $this->headers[$header] = $value ?? true;
        }

        return $this->headers;
    }

    public function to(?string $email): array
    {
        $this->to[] = $email;

        return $this->to;
    }

    public function getMessage(): string
    {
        if ($this->type !== self::EMAIL_MULTIPART) {
            return Strings::interpolate(self::normalize($this->rawMessage), $this->context);
        }

        $this->headers['Content-Type'] = Strings::interpolate(
            $this->headers['Content-Type'],
            ['boundary' => $this->boundary]
        );
        $parts = [$this->addPart(self::DEFAULT_HEADERS[self::EMAIL_HTML], $this->getMessage(), self::EMAIL_HTML)];
        foreach ($this->files as $file => $path) {
            $parts[] = $this->addPart(
                [
                    'Content-Type'              => Strings::interpolate(
                        self::DEFAULT_HEADERS[self::EMAIL_OCTET_STREAM]['Content-Type'],
                        ['name' => $file]
                    ),
                    'Content-Transfer-Encoding' => 'base64',
                    'Content-Disposition'       => 'attachment',
                ],
                chunk_split(base64_encode(file_get_contents($path)))
            );
        }
        return implode(self::MAIL_EOL . self::MAIL_EOL, $parts);
    }

    public static function normalize(string $message): string
    {
        return str_contains($message, self::MAIL_EOL)
            ? $message
            : str_replace(self::UNIX_EOL, self::MAIL_EOL, $message);
    }

    public function send(): bool
    {
        if (empty($this->to)) {
            return false;
        }

        $message = $this->getMessage();
        $headers = $this->headers + self::DEFAULT_HEADERS[$this->type];

        return mail(
            implode(',', $this->to),
            $this->subject,
            $message,
            $headers
        );
    }

    public function addPart(array $headers, string $body): string
    {
        if ($this->type !== self::EMAIL_MULTIPART) {
            return '';
        }
        $headers = implode(
            self::MAIL_EOL,
            array_map(
                fn(string $headerName, string $headerValue) => "$headerName: $headerValue",
                array_keys($headers),
                array_values($headers)
            )
        );

        return self::normalize(
            <<<§
            --{$this->boundary}
            $headers
            
            $body
            
            --{$this->boundary}--
            §
        );
    }

}

;
