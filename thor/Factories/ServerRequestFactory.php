<?php

namespace Thor\Factories;

use Thor\Http\Uri;
use Thor\Stream\Stream;
use Thor\Http\ProtocolVersion;
use Thor\Http\Request\HttpMethod;
use Thor\Http\Request\UploadedFile;
use Thor\Http\Request\ServerRequest;
use Thor\Http\Request\ServerRequestInterface;

final class ServerRequestFactory extends RequestFactory
{

    public static function createFromGlobals(): ServerRequestInterface
    {
        $version = explode('/', $_SERVER['SERVER_PROTOCOL'])[1] ?? '1.1';

        return new ServerRequest(
                           ProtocolVersion::from($version),
                           getallheaders(),
                           new Stream(Stream::fileOpen('php://temp', 'r+')),
                           HttpMethod::from($_SERVER['REQUEST_METHOD'] ?? 'GET'),
                           Uri::fromGlobals(),
            cookies:       $_COOKIE,
            parsedBody:    $_POST,
            queryParams:   $_GET,
            serverParams:  $_SERVER,
            uploadedFiles: UploadedFile::normalizeFiles($_FILES)
        );
    }

}
