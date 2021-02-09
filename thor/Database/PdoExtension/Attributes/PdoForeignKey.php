<?php

namespace Thor\Database\PdoExtension\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class PdoForeignKey
{

    private string $name;

    public function __construct(
        private string $className,
        private array $columnNames,
        ?string $name = null
    ) {
        $this->name = $name ?? 'fk_' . basename($this->className);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getColumnNames(): array
    {
        return $this->columnNames;
    }


}
