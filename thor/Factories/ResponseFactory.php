<?php

namespace Thor\Factories;

use Thor\Http\Uri;
use Thor\Stream\Stream;
use Thor\Http\UriInterface;
use Thor\Http\ProtocolVersion;
use Thor\Http\Response\Response;
use Thor\Http\Response\HttpStatus;

final class ResponseFactory extends Factory
{

    public function __construct(private HttpStatus $status, private array $headers = [])
    {
    }

    public function produce(array $options = []): Response
    {
        return match ($this->status) {
            HttpStatus::NOT_FOUND => self::notFound($options['message'] ?? ''),
            HttpStatus::FOUND => self::createRedirection(
                is_string($url = $options['uri'] ?? '/') ? Uri::create($url) : $url
            ),
            HttpStatus::OK => self::ok($options['body'] ?? ''),
            default => Response::create('', $this->status, $this->headers)
        };
    }


    public static function json(
        mixed $data,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): Response {
        return new Response(
            $version,
            $headers + ['Content-Type' => 'application/json; charset=UTF-8'],
            Stream::create(json_encode($data, JSON_THROW_ON_ERROR)),
            $status
        );
    }

    public static function createRedirection(UriInterface $uri): Response
    {
        return Response::create('', HttpStatus::FOUND, ['Location' => "$uri"]);
    }

    public static function notFound(string $message = ''): Response
    {
        return Response::create($message, HttpStatus::NOT_FOUND);
    }

    public static function ok(string $body = ''): Response
    {
        return Response::create($body, HttpStatus::OK);
    }

}
