<?php

namespace App;

use Thor\Message\Email;
use Thor\Process\Command;
use Thor\Message\Part\ContentPart;

final class Test extends Command
{
    public function execute(): void
    {
        $email = new Email('trehinos@gmail.com', 'Yes', 'No', images: [__FILE__], files: [__FILE__]);
        $email->setPreamble('Préambule...');
        $email->parts[] = new ContentPart('Contenu de test...');
        $result = $email->send('trehinos@gmail.com');
        echo "$email";
        echo "\n\n" . ($result ? "\e[32mEnvoyé !\e[0m" : "\e[31mEchec...\e[0m") . "\n";
    }
}
