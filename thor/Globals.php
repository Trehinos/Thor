<?php

namespace Thor;

final class Globals
{
    const VERSION = Thor::VERSION;
    const CODE_DIR = __DIR__ . '/../';
    const RESOURCES_DIR = self::CODE_DIR . 'app/res/';
    const CONFIG_DIR = self::RESOURCES_DIR . 'config/';
    const STATIC_DIR = self::RESOURCES_DIR . 'static/';
}
