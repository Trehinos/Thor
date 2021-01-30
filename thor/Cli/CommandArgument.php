<?php

namespace Thor\Cli;

/**
 * Class CommandArgument: structure
 * @package Thor\Cli
 *
 * @since 2020-09
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
final class CommandArgument
{

    /**
     * @var string argument identifier
     */
    public string $name;

    /**
     * @var string one letter to use the argument with a command
     */
    public string $shortArgument;

    /**
     * @var string full length argument form
     */
    public string $longArgument;

    /**
     * @var string a comprehensible description of the argument
     */
    public string $description;

    /**
     * @var bool true if the next argument in the command is the value of this argument
     */
    public bool $hasValue;

    /**
     * CommandArgument constructor.
     * 
     * @param string $name argument identifier
     * @param string $description a comprehensible description of the argument (default '')
     * @param bool $hasValue true if the next argument in the command is the value of this argument (default false)
     * @param string|null $longArgument one letter to use the argument with a command (default null -> $name)
     * @param string|null $shortArgument full length argument form (default null -> $name[0])
     */
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
