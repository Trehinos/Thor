<?php


namespace Thor\Framework\Actions;

use Thor\Framework\Factories\RequestFactory;
use Thor\Framework\Factories\ResponseFactory;
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

    #[Route('api-index', '/', [HttpMethod::GET, HttpMethod::POST])]
    public function index(): Response
    {
        $requestTest = RequestFactory::post($uri = Uri::create('https://test.com#zergjbezrjfkgzehf'), [
            'Truc' => 'Bidule',
            'Machin' => 'Chose'
        ]);
        dump($uri->getHost());
        echo nl2br($requestTest->getRaw());

        echo "<hr>";
        $responseTest = ResponseFactory::json(['test-response' => 'This is a test...']);
        echo nl2br($responseTest->getRaw());
        exit;

        return ResponseFactory::json(['test-response' => 'This is a test...']);
    }

}

