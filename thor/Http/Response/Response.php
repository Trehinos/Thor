<?php

/**
 * @package          Trehinos/Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Http\Response;

use Thor\Http\Message;
use Thor\Stream\Stream;
use JetBrains\PhpStorm\Pure;
use Thor\Http\ProtocolVersion;
use Thor\Stream\StreamInterface;

class Response extends Message implements ResponseInterface
{

    #[Pure]
    public function __construct(
        ProtocolVersion $version,
        array $headers,
        StreamInterface $body,
        private HttpStatus $status
    ) {
        parent::__construct($version, $headers, $body);
    }

    public static function create(
        string $body,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): self {
        return new self($version, $headers, Stream::create($body), $status);
    }

    public static function createFromStatus(
        HttpStatus $status,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): self {
        return new self($version, $headers, Stream::create($status->normalized()), $status);
    }

    public function getStatusCode(): int
    {
        return $this->status->value;
    }

    #[Pure]
    public function withStatus(HttpStatus $status): static
    {
        return new self($this->getProtocolVersion(), $this->getHeaders(), $this->getBody(), $status);
    }

    public function getReasonPhrase(): string
    {
        return $this->status->name;
    }

    public function getStatus(): HttpStatus
    {
        return $this->status;
    }

}
