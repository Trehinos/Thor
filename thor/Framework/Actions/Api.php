<?php


namespace Thor\Framework\Actions;

use Thor\Factories\RequestFactory;
use Thor\Factories\ResponseFactory;
use Thor\Http\{Controllers\HttpController, Request\HttpMethod, Response\Response, Routing\Route, Uri};

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
        $test = RequestFactory::put(Uri::create("#test"), ['testVar' => 'testValue']);
        echo nl2br($test->getRaw());
        exit;

        return ResponseFactory::json(['test-response' => 'This is a test...']);
    }

}

