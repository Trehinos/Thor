<?php

namespace Thor\Database\PdoTable\PdoTable;

use Thor\Database\PdoTable\CrudHelper;

/**
 * This class is made to convert various forms of data around PdoRowInterface.
 *
 * Converts :
 * - `PdoRowInterface` `<->` array
 * - `PdoRowInterface` `<->` json string
 *
 * @package   Thor\Database\PdoTable
 *
 *
 * @template T
 * @since     2020-10
 * @version   1.0
 * @author    Trehinos
 * @copyright Author
 * @license   MIT
 */
final class PdoRowConverter
{

    public function __construct(
        private PdoRowInterface $pdoRow
    ) {
    }

    /**
     * @param class-string<T> $className
     * @param string $json
     * @param mixed  ...$constructorArguments
     *
     * @return static
     */
    public static function fromJson(string $className, string $json, mixed ...$constructorArguments): self
    {
        return new self(CrudHelper::instantiateFromRow($className, json_decode($json), false, ...$constructorArguments));
    }

    /**
     * @param class-string<T> $className
     * @param array $data
     * @param mixed  ...$constructorArguments
     *
     * @return static
     */
    public static function fromArray(string $className, array $data, mixed ...$constructorArguments): self
    {
        return new self(CrudHelper::instantiateFromRow($className, $data, false, ...$constructorArguments));
    }

    /**
     * Gets the instantiated PdoRow.
     *
     * @return T
     */
    public function get(): PdoRowInterface
    {
        return $this->pdoRow;
    }

    /**
     * Gets the json string of the current PdoRow.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->pdoRow->toPdoArray();
    }

}
