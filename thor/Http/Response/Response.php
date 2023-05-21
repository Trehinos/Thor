<?php

namespace Thor\Http\Response;

use JetBrains\PhpStorm\Pure;
use Thor\FileSystem\Stream\{Stream};
use Thor\Http\{Message, ProtocolVersion};
use Thor\FileSystem\Stream\StreamInterface;

/**
 * Describes a Response of an HTTP Request.
 *
 * @package          Thor/Http/Response
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Response extends Message implements ResponseInterface
{

    /**
     * @param ProtocolVersion $version
     * @param array           $headers
     * @param StreamInterface $body
     * @param HttpStatus      $status
     */
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
     * Gets the string corresponding the current request.
     *
     * @return string
     */
    public function getRaw(): string
    {
        $requestStr =
            "HTTP/{$this->getProtocolVersion()->value} {$this->getStatusCode()} {$this->getStatus()->normalized()} \r\n";

        foreach ($this->getHeaders() as $name => $value) {
            $requestStr .= "$name: " . (is_array($value) ?  implode(', ', $value) : $value) . "\r\n";
        }

        $requestStr .= "\r\n" . $this->getBody()->getContents();

        return $requestStr;
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
