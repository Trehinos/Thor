<?php

namespace Evolution;

use Thor\Http\Response\Response;
use Thor\Http\Routing\Route;
use Thor\Web\WebController;

class EvolutionActions extends WebController
{

    #[Route('game', '/evolution/game')]
    public function game(): Response
    {
        return $this->twigResponse(
            'evolution/game.html.twig',
            []
        );
    }

    #[Route('game', '/evolution/game/city/$city', parameters: ['city' => '.+'])]
    public function city(string $city): Response
    {
        return $this->twigResponse(
            'evolution/game.html.twig',
            []
        );
    }

}
