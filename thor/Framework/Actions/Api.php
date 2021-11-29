<?php


namespace Thor\Framework\Actions;

use Thor\Factories\ResponseFactory;
use Thor\Http\{Controllers\HttpController, Request\HttpMethod, Response\Response, Routing\Route};

/**
 * HttpController to test the api.php entry point.
 *
 * @internal
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Api extends HttpController
{

    #[Route('api-index', '/', HttpMethod::GET)]
    public function index(): Response
    {
        return ResponseFactory::json(['test-response' => 'This is a test...']);
    }

}

