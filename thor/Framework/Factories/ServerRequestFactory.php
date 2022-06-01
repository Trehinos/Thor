<?php

namespace Thor\Framework\Factories;

use Thor\Http\Uri;
use Thor\Stream\Stream;
use Thor\Http\ProtocolVersion;
use Thor\Http\Request\HttpMethod;
use Thor\Http\Request\UploadedFile;
use Thor\Http\Request\ServerRequest;
use Thor\Http\Request\ServerRequestInterface;

/**
 * A factory to create a ServerRequest from globals.
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class ServerRequestFactory
{

    private function __construct()
    {
    }

    /**
     * @return ServerRequestInterface
     */
    public static function createFromGlobals(): ServerRequestInterface
    {
        $version = explode('/', $_SERVER['SERVER_PROTOCOL'])[1] ?? '1.1';

        return new ServerRequest(
                           ProtocolVersion::from($version),
                           getallheaders(),
                           new Stream(Stream::openFile('php://temp', 'r+')),
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
