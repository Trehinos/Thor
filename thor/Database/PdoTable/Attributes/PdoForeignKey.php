<?php

/**
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class PdoForeignKey
{

    private string $name;

    public function __construct(
        private string $className,
        private array $targetColumns,
        private array $localColumns,
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

    public function getTargetColumns(): array
    {
        return $this->targetColumns;
    }

    public function getLocalColumns(): array
    {
        return $this->localColumns;
    }


}
