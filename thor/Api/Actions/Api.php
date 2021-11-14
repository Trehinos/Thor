<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Actions;

use Thor\Http\Request\HttpMethod;
use Thor\Http\Response\Response;
use Thor\Http\Routing\Route;
use Thor\Factories\ResponseFactory;
use Thor\Http\Controllers\HttpController;

final class Api extends HttpController
{

    #[Route('api-index', '/', HttpMethod::GET)]
    public function index(): Response
    {
        return ResponseFactory::json(['test-response' => 'This is a test...']);
    }

}

