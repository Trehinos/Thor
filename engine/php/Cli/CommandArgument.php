<?php

namespace Thor\Cli;

final class CommandArgument
{

    private string $shortArgument;

    private string $longArgument;

    private string $description;

    private bool $hasValue;

    public function __construct(
        string $longArgument,
        ?string $shortArgument = null,
        string $description = '',
        bool $hasValue = false
    ) {
        $this->longArgument = $longArgument;
        $this->shortArgument = $shortArgument ?? substr($longArgument, 0, 1);
        $this->description = $description;
        $this->hasValue = $hasValue;
    }

}
