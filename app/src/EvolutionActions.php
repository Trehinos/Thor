<?php

namespace Evolution;

use Evolution\Common\Resources;
use Evolution\DataModel\Resource\Count;
use Evolution\DataModel\Resource\Resource;
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
        $res = Resources::allResources();
        $counts = array_combine(
            array_keys($res),
            array_map(fn (Resource $res) => new Count($res, 0.0), $res)
        );
        $counts['wood']->set(215658);
        $counts['stone']->set(111455858);
        $counts['water']->set(88745);
        $counts['copper']->set(44784);

        return $this->twigResponse(
            'evolution/game.html.twig',
            [
                'resources' => $counts
            ]
        );
    }

}
