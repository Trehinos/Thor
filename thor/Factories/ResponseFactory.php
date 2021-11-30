<?php

namespace Thor\Factories;

use JsonException;
use Thor\Stream\Stream;
use Thor\Http\UriInterface;
use Thor\Http\ProtocolVersion;
use Thor\Http\Response\Response;
use Thor\Http\Response\HttpStatus;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * A factory to create standard Responses.
 *
 * @link             https://developer.mozilla.org/fr/docs/Web/HTTP/Status
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class ResponseFactory
{

    /**
     * @throws JsonException
     */
    public static function json(
        mixed $data,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): Response {
        return self::text(json_encode($data, JSON_THROW_ON_ERROR), $status, $headers, $version);
    }

    public static function text(
        string $body,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): Response {
        return new Response(
            $version,
            $headers + ['Content-Type' => 'text/plain; charset=UTF-8'],
            Stream::create($body),
            $status
        );
    }

    public static function ok(string $body = ''): Response
    {
        return Response::create($body);
    }

    public static function noContent(): Response
    {
        return Response::create('', HttpStatus::NO_CONTENT);
    }

    /**
     * 301 MOVED PERMANENTLY
     *
     * The resource has been moved permanently with no guaranty on the method and body idempotence.
     *
     * @param UriInterface $uri
     *
     * @return Response
     */
    public static function moved(UriInterface $uri): Response
    {
        return Response::create('', HttpStatus::MOVED_PERMANENTLY, ['Location' => "$uri"]);
    }

    /**
     * 302 FOUND
     *
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
     * 303 SEE OTHER
     *
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

    /**
     * 307 TEMPORARY REDIRECT
     *
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
     * 308 PERMANENT REDIRECT
     *
     * Used for a permanent redirect with guaranties on the method and body idempotence.
     *
     * @param UriInterface $uri
     *
     * @return Response
     */
    public static function permanentRedirect(UriInterface $uri): Response
    {
        return Response::create('', HttpStatus::TEMPORARY_REDIRECT, ['Location' => "$uri"]);
    }

    /**
     * 401 UNAUTHORIZED
     *
     * "Unauthenticated". MUST provide a way to authenticate.
     *
     * @param string  $type
     * @param ?string $realm protected zone description.
     * @param bool    $utf8
     *
     * @return Response
     */
    public static function unauthorized(
        #[ExpectedValues([
            'Basic',
            'Bearer',
            'Digest',
            'HOBA',
            'Mutual',
            'Negotiate',
            'OAuth',
            'SCRAM-SHA-1',
            'SCRAM-SHA-256',
            'vapid',
        ])]
        string $type,
        ?string $realm = null,
        bool $utf8 = true
    ): Response {
        $elems = [];
        if ($realm) {
            $elems[] = "realm=\"$realm\"";
        }
        if ($utf8) {
            $elems[] = 'charset="UTF-8"';
        }

        return Response::create('', HttpStatus::UNAUTHORIZED, [
            'WWW-Authenticate' => $type . (!empty($elems) ? (' ' . implode(', ', $elems)) : ''),
        ]);
    }

    /**
     * 403 FORBIDDEN
     *
     * Signal to the client that the resource is not accessible.
     *
     * @return Response
     */
    public static function forbidden(): Response
    {
        return Response::create('', HttpStatus::FORBIDDEN);
    }

    /**
     * 404 NOT FOUND
     *
     * Signal to the client that the resource is not found at this Uri.
     *
     * @param string $message
     *
     * @return Response
     */
    public static function notFound(string $message = ''): Response
    {
        return Response::create($message, HttpStatus::NOT_FOUND);
    }

    /**
     * 405 METHOD NOT ALLOWED
     *
     * Used to signal to a client that the request method is not valid for this resource.
     *
     * @param array|string $allow
     *
     * @return Response
     */
    public static function methodNotAllowed(array|string $allow): Response
    {
        if (is_array($allow)) {
            $allow = implode(', ', $allow);
        }
        return Response::create('', HttpStatus::METHOD_NOT_ALLOWED, ['Allow' => strtoupper($allow)]);
    }

    /**
     * 409 CONFLICT
     *
     * Used to signal to a client that the request generate conflict with the current state of the server.
     *
     * @param string $payload description on how to resolve the conflict.
     *
     * @return Response
     */
    public static function conflict(string $payload): Response
    {
        return Response::create($payload, HttpStatus::CONFLICT);
    }

}
