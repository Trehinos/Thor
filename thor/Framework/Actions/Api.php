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
        $requestTest = RequestFactory::formPost(Uri::create('test.com/test.php#test'), [
            'Truc' => 'Bidule',
            'Machin' => 'Chose'
        ]);
        echo nl2br($requestTest->getRaw());

        echo "<hr>";
        $responseTest = ResponseFactory::json(['test-response' => 'This is a test...']);
        echo nl2br($responseTest->getRaw());
        exit;

        return ResponseFactory::json(['test-response' => 'This is a test...']);
    }

}

