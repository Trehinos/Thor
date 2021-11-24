<?php

namespace Thor\Security\Configuration;

enum IdentityType:string
{
    case TYPE_USERPWD = 'username-password';
    case TYPE_TOKEN = 'login-token';
    case TYPE_BOTH = 'both';
}
