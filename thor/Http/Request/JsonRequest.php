<?php

namespace Thor\Http\Request;

use JsonException;
use Thor\Http\Uri;
use Thor\Stream\Stream;
use Thor\Http\ProtocolVersion;

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
            $headers,
            Stream::create(self::encode($data)),
            $method,
            Uri::create($target)
        );
    }

    /**
     * @throws JsonException
     */
    public static function encode(mixed $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function getData(): array|string|int|bool|null
    {
        return self::decode($this->getBody()->getContents());
    }

    /**
     * @throws JsonException
     */
    public static function decode(string $json): array|string|int|bool|null
    {
        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

}
