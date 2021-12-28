<?php

namespace Thor\Configuration;

final class TwigConfiguration extends ConfigurationFromFile
{

    public function __construct()
    {
        parent::__construct('twig');
    }

}