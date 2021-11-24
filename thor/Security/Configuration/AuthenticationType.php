<?php

namespace Thor\Security\Configuration;

enum AuthenticationType:string
{
    case AUTH_SESSION = 'session';
    case AUTH_HEADER = 'authentication-token';
}
