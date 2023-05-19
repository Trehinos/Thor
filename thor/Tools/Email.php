<?php

namespace Thor\Tools;

use Thor\Message\Email as MessageEmail;

/**
 * Thor\Message facade.
 */
class Email
{

    private function __construct() {

    }

    public static function complete(
        string $subject,
        string $body,
        string $from,
        array $files = [],
        array $images = [],
    ) : MessageEmail {
        return new MessageEmail($from, $subject, $body, $images, $files);
    }

}
