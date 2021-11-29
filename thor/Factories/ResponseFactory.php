<?php

namespace Thor\Factories;

use Thor\Stream\Stream;
use Thor\Http\UriInterface;
use Thor\Http\ProtocolVersion;
use Thor\Http\Response\Response;
use Thor\Http\Response\HttpStatus;

/**
 * A factory to standard Responses.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class ResponseFactory
{

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
