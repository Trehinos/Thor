<?php

/**
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class PdoColumn
{

    /**
     * @var callable
     */
    private $toSqlValue;

    /**
     * @var callable
     */
    private $toPhpValue;

    public function __construct(
        private string $name,
        private string $sqlType,
        private string $phpType,
        private bool $nullable = true,
        private mixed $defaultValue = null,
        ?callable $toSqlValue = null,
        ?callable $toPhpValue = null
    ) {
        $this->toSqlValue = $toSqlValue;
        $this->toPhpValue = $toPhpValue;
    }

    public function getDefault(): mixed
    {
        return $this->defaultValue;
    }

    public function toPhp(mixed $sqlValue): mixed
    {
        return null === $this->toPhpValue ?
            $sqlValue :
            ($this->toPhpValue)($sqlValue);
    }

    public function toSql(mixed $phpValue): mixed
    {
        return null === $this->toSqlValue ?
            $phpValue :
            ($this->toSqlValue)($phpValue);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSqlType(): string
    {
        return $this->sqlType;
    }

    public function getPhpType(): string
    {
        return $this->phpType;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    #[Pure] public function getSql(): string
    {
        $nullStr = $this->nullable ? '' : ' NOT NULL';
        $defaultStr = ($this->getDefault() === null) ? '' : " DEFAULT {$this->getDefault()}";
        return "{$this->getName()} {$this->getSqlType()}$nullStr$defaultStr";
    }

}
