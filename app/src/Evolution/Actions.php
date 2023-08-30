<?php

namespace App\Evolution;

use Thor\Configuration\ConfigurationFromResource;
use Thor\Http\Response\Response;
use Thor\Http\Routing\Route;
use Thor\Web\WebController;

final class Actions extends WebController
{

    #[Route('evolution', '/evolution')]
    public function view(): Response
    {
        return $this->twigResponse(
            'evolution/interface.html.twig',
            [
                'data' => [
                    'cityName' => 'Ville de test',
                    'resources' => ConfigurationFromResource::fromFile('evolution/resources')
                ]
            ]
        );
    }

}
