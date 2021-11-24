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

    /**
     * @param array $options ['status' => HttpStatus, 'headers' => array] +
     *                       ['body' => string] || 404:['message' => string] || 302:['uri' => UriInterface|string]
     *
     * @return Response
     */
    public function produce(array $options = []): Response
    {
        $status = $options['status'] ?? $this->status;
        $headers = ($options['headers'] ?? []) + $this->headers;
        return match ($status) {
            HttpStatus::NOT_FOUND => self::notFound($options['message'] ?? ''),
            HttpStatus::FOUND => self::createRedirection(
                is_string($url = $options['uri'] ?? '/') ? Uri::create($url) : $url
            ),
            HttpStatus::OK => self::ok($options['body'] ?? ''),
            default => Response::create($options['body'] ?? '', $status, $headers)
        };
    }

    public static function notFound(string $message = ''): Response
    {
        return Response::create($message, HttpStatus::NOT_FOUND);
    }

    public static function createRedirection(UriInterface $uri): Response
    {
        return Response::create('', HttpStatus::FOUND, ['Location' => "$uri"]);
    }

    public static function ok(string $body = ''): Response
    {
        return Response::create($body);
    }

}
