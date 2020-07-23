<?php

namespace Thor\Validation\Filters;

use Thor\Http\Server;
use Thor\Validation\FilterInterface;

class PostVarRegex implements FilterInterface
{

    private string $regExp;

    public function __construct(string $regExp = '.*')
    {
        $this->regExp = $regExp;
    }

    public function filter($post_var_name)
    {
        return Server::post(
            $post_var_name,
            null,
            FILTER_VALIDATE_REGEXP,
            [
                'options' => ['regexp' => $this->regExp]
            ]
        );
    }

}
