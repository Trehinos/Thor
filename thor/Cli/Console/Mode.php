<?php

namespace Thor\Cli\Console;

enum Mode: int
{
    case RESET = 0;
    case BRIGHT = 1;
    case DIM = 2;
    case UNDERSCORE = 3;
    case BLINK = 5;
    case REVERSE = 7;
    case HIDDEN = 8;
}
