<?php

namespace Thor\Database\PdoExtension;

final class MultiRequester extends PdoRequester
{

    private PdoCollection $collection;

    public function __construct(PdoHandler $defaultHandler)
    {
        parent::__construct($defaultHandler);
        $this->collection = new PdoCollection();
        $this->collection->add('default', $defaultHandler);
    }

    public function addHandler(string $name, PdoHandler $handler): void
    {
        $this->collection[$name] = $handler;
    }

    public function multiRequest(string $query, array $parameters): array
    {

    }

}
