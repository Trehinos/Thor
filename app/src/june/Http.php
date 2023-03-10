<?php

namespace June;

use Thor\Web\WebController;
use Thor\Http\Routing\Route;
use Thor\Http\Response\Response;

final class Http extends WebController
{

    #[Route('ngine', '/ngine')]
    public function engine(): Response
    {
        return $this->twigResponse('june/ngine.html.twig');
    }

}