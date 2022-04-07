<?php

namespace Thor\Tools\Promise;

enum PromiseState
{

    case PENDING;
    case FULFILLED;
    case REJECTED;

}
