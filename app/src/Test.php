<?php

namespace App;

use Thor\Message\Email;
use Thor\Process\Command;

final class Test extends Command
{
    public function execute(): void
    {
        $email = new Email('trehinos@gmail.com', 'Subject', 'Body', images: [__FILE__], files: [__FILE__]);
        $email->send('$to');
    }
}
