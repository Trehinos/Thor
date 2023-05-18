<?php

namespace App;

use Thor\Cli\Command\Command;

final class Test extends Command
{
    public function execute(): void
    {
        $enc = mb_list_encodings();
        print_r(
            array_combine(
                $enc,
                array_map(
                    fn($aliases) => implode(', ', $aliases),
                    array_map(
                        fn($e) => mb_encoding_aliases($e),
                        $enc
                    )
                )
            ),
        );
    }
}
