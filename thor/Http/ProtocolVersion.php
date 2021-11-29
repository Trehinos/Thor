<?php

namespace Thor\Http;

/**
 * This enumeration contains different valid versions of the HTTP protocol.
 */
enum ProtocolVersion:string
{

    case HTTP09 = '0.9';
    case HTTP10 = '1.0';
    case HTTP11 = '1.1';
    case HTTP20 = '2.0';
    case HTTP30 = '3.0';

}
