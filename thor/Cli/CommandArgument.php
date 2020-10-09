<?php

namespace Thor\Cli;

final class CommandArgument
{

    public string $name;

    public string $shortArgument;

    public string $longArgument;

    public string $description;

    public bool $hasValue;

    public function __construct(
        string $name,
        string $description = '',
        bool $hasValue = false,
        ?string $longArgument = null,
        ?string $shortArgument = null
    ) {
        $this->name = strtolower($name);
        $this->longArgument = strtolower($longArgument ?? $name);
        $this->shortArgument = $shortArgument ?? substr($longArgument, 0, 1);
        $this->description = $description;
        $this->hasValue = $hasValue;
    }

}
