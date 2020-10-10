<?php

namespace Thor\Html\Form;

use Thor\Database\PdoExtension\PdoRowInterface;
use Thor\Html\HtmlTag;
use Thor\Http\Request;

abstract class Form extends HtmlTag implements FormInterface
{

    private array $data;

    public function __construct(string $action, string $method = Request::POST)
    {
        parent::__construct('form', true, ['action' => $action, 'method' => $method]);
    }

    abstract public static function formDefinition(): array;

    /**
     * @param FieldInterface[] $data
     */
    public function setData(array $data): void
    {
        $this->data = $data + $this->data;
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields(): array
    {
        return $this->data;
    }

    public function getChildren(): array
    {
        return array_values(static::formDefinition());
    }

    /**
     * @param PdoRowInterface $row
     */
    public static function setFromPdoRow(PdoRowInterface $row): void
    {

    }

}
