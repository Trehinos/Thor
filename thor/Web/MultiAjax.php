<?php

namespace Thor\Web;

use Thor\Http\Response\Response;
use Thor\Http\Response\HttpStatus;

class MultiAjax extends WebController
{

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
    }

    protected function ajaxResponse(array $data = [], HttpStatus $status = HttpStatus::OK): Response
    {
        return Response::create(
            '',
            [],
        );
    }

}
