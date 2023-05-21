<?php

namespace Thor\Framework\Actions;

use Thor\Framework\Globals;
use Thor\Http\Web\WebController;
use Symfony\Component\Yaml\Yaml;
use Thor\Http\{Routing\Route, Response\Response, Request\HttpMethod};

/**
 * WebController, this is the main controller to serve Responses for the main routes of Thor.
 *
 * @internal
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Main extends WebController
{

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('index', '/', HttpMethod::GET)]
    public function index(): Response
    {
        return $this->twigResponse('thor/page.html.twig', ['menuItem' => $this->get('menuItem')]);
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('index-page', '/index', HttpMethod::GET)]
    public function indexPage(): Response
    {
        $this->addMessage("Ceci est un message de test", "Message TEST", "default", "Thor V1");
        return $this->twigResponse('thor/pages/index.html.twig', retrieveMessages: true);
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('help', '/help', HttpMethod::GET)]
    public function help(): Response
    {
        return $this->twigResponse('thor/help.html.twig');
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('documentation', '/documentation', HttpMethod::GET)]
    public function documentationPage(): Response
    {
        return $this->twigResponse('thor/pages/documentation.html.twig');
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('menu')]
    public function menu(): Response
    {
        return $this->twigResponse(
            'menu.html.twig',
            [
                'menu' => Yaml::parse(file_get_contents(Globals::STATIC_DIR . 'menu.yml')),
            ]
        );
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('changelog', '/changelog', HttpMethod::GET)]
    public function changelog(): Response
    {
        return $this->twigResponse('thor/pages/changelog.html.twig');
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('about', '/about', HttpMethod::GET)]
    public function about(): Response
    {
        return $this->twigResponse('thor/pages/about.html.twig');
    }

    /**
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    #[Route('legal', '/legal', HttpMethod::GET)]
    public function legal(): Response
    {
        return $this->twigResponse('thor/pages/legal.html.twig');
    }

}
