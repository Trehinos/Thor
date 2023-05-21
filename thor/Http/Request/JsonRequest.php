<?php

namespace Thor\Http\Request;

use JsonException;
use Thor\Http\Uri;
use Thor\Http\ProtocolVersion;
use Thor\FileSystem\Stream\Stream;

/**
 * A class too create Json APIs.
 *
 * @see              ServerRequestFactory to create a ServerRequest from globals
 *
 * @package          Thor/Http/Request
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class JsonRequest extends Request
{

    /**
     * @throws JsonException
     */
    public function __construct(
        HttpMethod $method,
        string $target,
        mixed $data = null,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ) {
        parent::__construct(
            $version,
            $headers + ['Content-Type' => 'application/json'],
            Stream::create(self::encode($data)),
            $method,
            Uri::create($target)
        );
    }

    /**
     * Encodes specified data to JSON.
     *
     * @throws JsonException
     */
    public static function encode(mixed $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * Get data as a decoded JSON (it can be any scalar or array).
     *
     * @throws JsonException
     */
    public function getData(): array|string|float|int|bool|null
    {
        return self::decode($this->getBody()->getContents());
    }

    /**
     * Decodes the specified JSON string to a scalar or an array.
     *
     * @throws JsonException
     */
    public static function decode(string $json): array|string|float|int|bool|null
    {
        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

}
