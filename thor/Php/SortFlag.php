<?php

namespace Thor\Php;

enum SortFlag:int
{
    case REGULAR = 0;
    case NUMERIC = 1;
    case STRING = 2;
    case STRING_CI = 10;
    case LOCALE_STRING = 5;
    case NATURAL = 6;
    case NATURAL_CI = 14;
}
