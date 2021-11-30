<?php

namespace Thor\Http\Response;

use JetBrains\PhpStorm\Pure;
use Thor\Http\{Message, ProtocolVersion};
use Thor\Stream\{Stream, StreamInterface};

/**
 * Describes a Response of an HTTP Request.
 *
 * @package          Thor/Http/Response
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
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

    /**
     * Creates a new Response with default parameters.
     *
     * @param string          $body
     * @param HttpStatus      $status
     * @param array           $headers
     * @param ProtocolVersion $version
     *
     * @return static
     */
    public static function create(
        string $body,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): self {
        return new self($version, $headers, Stream::create($body), $status);
    }

    /**
     * Creates a Response from its status.
     *
     * The body will be filled with the reason phrase corresponding the status.
     *
     * @param HttpStatus      $status
     * @param array           $headers
     * @param ProtocolVersion $version
     *
     * @return static
     */
    public static function createFromStatus(
        HttpStatus $status,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): self {
        return new self($version, $headers, Stream::create($status->normalized()), $status);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->status->value;
    }

    /**
     * @inheritDoc
     */
    public function withStatus(HttpStatus $status): static
    {
        return new self($this->getProtocolVersion(), $this->getHeaders(), $this->getBody(), $status);
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase(): string
    {
        return $this->status->normalized();
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): HttpStatus
    {
        return $this->status;
    }

}
