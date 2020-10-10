<?php

namespace Thor\Database\PdoExtension;

use Thor\Database\DefinitionHelper;

/**
 * Interface PdoRowInterface: represents a class which can be converted to an array.
 * @package Thor\Database\PdoExtension
 *
 * @since 2020-06
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
interface PdoRowInterface
{

    // <-> SQL Methods

    /**
     * @return array
     */
    public static function getPdoColumnsDefinitions(): array;

    /**
     * @return array the PDO array representation of class attributes.
     */
    public function toPdoArray(): array;

    /**
     * fromPdoArray(): set attributes of the object by the array
     *
     * @param array $pdoArray
     */
    public function fromPdoArray(array $pdoArray): void;


    // DEFAULT ACCESSORS & METHODS

    /**
     * @return int the row identifier. Used in foreign keys.
     */
    public function getId(): ?int;

    /**
     * @return string the public row identifier. Used in pages.
     */
    public function getPublicId(): ?string;

    /**
     * generate the public row identifier. Has to be unique.
     */
    public function generatePublicId(): void;

}
