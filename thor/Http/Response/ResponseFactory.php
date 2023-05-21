<?php

namespace Thor\Http\Response;

use DateTime;
use DateInterval;
use JsonException;
use DateTimeInterface;
use DateTimeImmutable;
use Thor\FileSystem\Stream\Stream;
use JetBrains\PhpStorm\ExpectedValues;
use Thor\Http\{UriInterface, ProtocolVersion};

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

    private function __construct()
    {
    }

    /**
     * Generates a custom JSON Response from specified data.
     *
     * @param mixed           $data
     * @param HttpStatus      $status
     * @param array           $headers
     * @param ProtocolVersion $version
     *
     * @return Response
     *
     * @throws JsonException
     */
    public static function json(
        mixed $data,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): Response {
        return self::text(
            json_encode($data, JSON_THROW_ON_ERROR),
            $status,
            $headers + ['Content-Type' => 'application/json; charset=UTF-8'],
            $version
        );
    }

    /**
     * Generates a custom plain text response.
     *
     * @param string          $body
     * @param HttpStatus      $status
     * @param array           $headers
     * @param ProtocolVersion $version
     *
     * @return Response
     */
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

    /**
     * Generates a custom HTML response.
     *
     * @param string          $body
     * @param HttpStatus      $status
     * @param array           $headers
     * @param ProtocolVersion $version
     *
     * @return Response
     */
    public static function html(
        string $body,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
        ProtocolVersion $version = ProtocolVersion::HTTP11
    ): Response {
        return self::text($body, $status, $headers + ['Content-Type' => 'text/html; charset=UTF-8'], $version);
    }

    /**
     * 200 OK
     *
     * OK response but respond with no content.
     *
     * @param string $body
     * @param array  $headers
     *
     * @return Response
     */
    public static function ok(string $body = '', array $headers = []): Response
    {
        return Response::create($body, HttpStatus::OK, $headers);
    }

    /**
     * 201 CREATED
     *
     * OK response, a resource has been created and the response will point it.
     *
     * @param string                 $location
     * @param string                 $etag
     * @param DateTimeInterface|null $lastModified
     *
     * @return Response
     */
    public static function created(
        string $location,
        string $etag = '',
        ?DateTimeInterface $lastModified = new DateTime()
    ): Response {
        return Response::create('', HttpStatus::CREATED, [
            'Location'      => $location,
            'ETag'          => $etag,
            'Last-Modified' => $lastModified->format(DateTimeInterface::RFC7231),
        ]);
    }

    /**
     * 202 ACCEPTED
     *
     * OK response but still processing.
     *
     * The payload MAY describe the request's status and point a status monitor.
     *
     * @param string $payload
     *
     * @return Response
     */
    public static function accepted(string $payload = ''): Response
    {
        return Response::create($payload, HttpStatus::ACCEPTED);
    }

    /**
     * 204 NO CONTENT
     *
     * OK response but respond with no content.
     *
     * @return Response
     */
    public static function noContent(): Response
    {
        return Response::create('', HttpStatus::NO_CONTENT);
    }

    /**
     * 205 RESET CONTENT
     *
     * OK response with no content but tell the user agent to reload the resource.
     *
     * @return Response
     */
    public static function resetContent(): Response
    {
        return Response::create('', HttpStatus::RESET_CONTENT);
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
     * 304 NOT MODIFIED
     *
     * The resource has not changed.
     *
     * Always redirect to a GET request.
     *
     * @param UriInterface|null $contentLocation
     * @param string            $eTag
     * @param DateTimeInterface $date
     * @param string            $cacheControl
     * @param DateInterval|null $expires
     * @param array|string      $vary
     *
     * @return Response
     */
    public static function notModified(
        ?UriInterface $contentLocation = null,
        string $eTag = '',
        DateTimeInterface $date = new DateTime(),
        string $cacheControl = 'no-cache',
        ?DateInterval $expires = null,
        array|string $vary = '*'
    ): Response {
        $headers = [
            'Date'          => $date->format(DateTimeInterface::RFC7231),
            'Cache-Control' => $cacheControl,
            'Vary'          => is_array($vary) ? implode(', ', $vary) : $vary,
        ];
        if ($contentLocation !== null) {
            $headers += ['Content-Location' => "$contentLocation"];
        }
        if ($eTag !== '') {
            $headers += ['ETag' => $eTag];
        }
        if ($expires !== null) {
            $headers += ['Expires' => (new DateTimeImmutable())->add($expires)->format(DateTimeInterface::RFC7231)];
        }

        return Response::create('', HttpStatus::NOT_MODIFIED, $headers);
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
        return Response::create('', HttpStatus::PERMANENT_REDIRECT, ['Location' => "$uri"]);
    }

    /**
     * 400 BAD REQUEST
     *
     * Signal to the client that the request contains some client's errors (syntax, message, etc.)
     *
     * @param string $message
     *
     * @return Response
     */
    public static function badRequest(string $message = ''): Response
    {
        return Response::create($message, HttpStatus::BAD_REQUEST);
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
     * Signal to the client that the user has not the permission to access the resource.
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

    /**
     * 500 INTERNAL SERVER ERROR
     *
     * Indicates that the server encountered an unexpected condition.
     *
     * @param string $payload
     *
     * @return Response
     */
    public static function internalServerError(string $payload = ''): Response
    {
        return Response::create($payload, HttpStatus::INTERNAL_SERVER_ERROR);
    }

    /**
     * 501 NOT IMPLEMENTED
     *
     * Indicates that the server does not support the functionality to fulfill the request.
     *
     * @return Response
     */
    public static function notImplemented(): Response
    {
        return Response::create('', HttpStatus::NOT_IMPLEMENTED);
    }

    /**
     * 503 SERVICE UNAVAILABLE
     *
     * Indicates that the server is not ready to respond. The response MUST contain a Retry-After field.
     *
     * @param DateTimeInterface|int $retryAfter
     *
     * @return Response
     */
    public static function serviceUnavailable(DateTimeInterface|int $retryAfter): Response
    {
        return Response::create('', HttpStatus::SERVICE_UNAVAILABLE, [
            'Retry-After' => is_int($retryAfter) ? $retryAfter : $retryAfter->format(DateTimeInterface::RFC7231),
        ]);
    }

}
