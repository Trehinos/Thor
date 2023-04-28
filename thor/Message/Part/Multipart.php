<?php

namespace Thor\Message\Part;

use Thor\Tools\Guid;
use Thor\Message\Headers\Headers;
use Thor\Message\Headers\ContentTransferEncoding;
class Multipart extends Part
{

    public const MIXED = 'mixed';
    public const ALTERNATIVE = 'alternative';
    public const DIGEST = 'digest';
    public const PARALLEL = 'parallel';
    public const RELATED = 'related';

    private string $preamble;

    /**
     * @param Part[]       $parts
     * @param string       $mediaSubType
     * @param string|null  $boundary
     * @param Headers|null $additionalHeaders
     *
     * @throws \Exception
     */
    public function __construct(
        protected array $parts = [],
        string $mediaSubType = self::MIXED,
        private ?string $boundary = null,
        ?ContentTransferEncoding $encoding = null,
        ?Headers $additionalHeaders = null
    ) {
        if (!in_array($encoding, [null, ContentTransferEncoding::BIT7, ContentTransferEncoding::BIT8, ContentTransferEncoding::BINARY])) {
            throw new \TypeError("ContentTransferEncoding::$encoding->name invalid for a multipart/* part.");
        }
        $this->boundary ??= bin2hex((new Guid())->get());
        parent::__construct(
            'multipart',
            $mediaSubType,
            "boundary={$this->boundary}",
            $encoding,
            $additionalHeaders
        );
        $this->preamble = '';
    }

    public function getBoundary(): string
    {
        return $this->boundary;
    }

    private function bound(): string
    {
        return "--{$this->getBoundary()}\r\n";
    }

    private function terminate(): string
    {
        return "--{$this->getBoundary()}--\r\n";
    }

    /**
     * @return Part[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    public final function getBody(): string
    {
        return $this->bound() .
            implode(
                $this->bound(),
                array_map(
                    fn(Part $part) => $this->chunk("$part"),
                     $this->getParts()
                )
            ) .
            $this->terminate();
    }

    public function setPreamble(string $preamble): void
    {
        $this->preamble = $this->chunk($preamble);
    }

    public function __toString(): string
    {
        $preamble = (trim($this->preamble) !== '') ? "{$this->preamble}\r\n\r\n" : '';
        return "{$this->getHeaders()}\r\n{$preamble}{$this->getBody()}\r\n";
    }

    public static function mixed(array $parts, ?Headers $headers = null): static
    {
        return new self($parts, Multipart::MIXED, additionalHeaders: $headers);
    }

    public static function related(array $parts, ?Headers $headers = null): static
    {
        return new self($parts, Multipart::RELATED, additionalHeaders: $headers);
    }

    public static function alternative(array $parts, ?Headers $headers = null): static
    {
        return new self($parts, Multipart::ALTERNATIVE, additionalHeaders: $headers);
    }

}
