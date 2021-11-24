<?php

namespace Thor\Security\Configuration;

enum IdentitySource:string
{

    case SOURCE_DB = 'database';    // DB
    case SOURCE_LDAP = 'ldap';      // Active directory
    case SOURCE_FILE = 'file';      // File
    case SOURCE_INTERNAL = 'internal';  // YML file

}
