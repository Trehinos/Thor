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
 * @link https://developer.mozilla.org/fr/docs/Web/HTTP/Status
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

    public static function ok(string $body = ''): Response
    {
        return Response::create($body);
    }

    public static function notFound(string $message = ''): Response
    {
        return Response::create($message, HttpStatus::NOT_FOUND);
    }

    /**
     * Used for a temporary redirect with no guaranties on the method and body idempotence.
     *
     * @param UriInterface $uri
     *
     * @return Response
     */
    public static function found(UriInterface $uri): Response
    {
        return Response::create('', HttpStatus::FOUND, ['Location' => "$uri"]);
    }

    /**
     * Used for a temporary redirect with guaranties on the method and body idempotence.
     *
     * @param UriInterface $uri
     *
     * @return Response
     */
    public static function temporaryRedirect(UriInterface $uri): Response
    {
        return Response::create('', HttpStatus::TEMPORARY_REDIRECT, ['Location' => "$uri"]);
    }

    /**
     * Used for redirection after a PUT or POST operation.
     *
     * Always redirect to a GET request.
     *
     * @param UriInterface $uri
     *
     * @return Response
     */
    public static function seeOther(UriInterface $uri): Response
    {
        return Response::create('', HttpStatus::SEE_OTHER, ['Location' => "$uri"]);
    }

    public static function noContent(): Response
    {
        return Response::create('', HttpStatus::NO_CONTENT);
    }

}
