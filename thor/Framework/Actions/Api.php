<?php

/**
 * HttpController to test the api.php entry point.
 *
 * @internal
 *
 * @package          Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Framework\Actions;

use Thor\Factories\ResponseFactory;
use Thor\Http\{Controllers\HttpController, Request\HttpMethod, Response\Response, Routing\Route};

/**
 * This is a test HttpController to test Api responses and the api.php entry point.
 * @internal
 */
final class Api extends HttpController
{

    #[Route('api-index', '/', HttpMethod::GET)]
    public function index(): Response
    {
        return ResponseFactory::json(['test-response' => 'This is a test...']);
    }

}

