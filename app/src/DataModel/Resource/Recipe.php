<?php

namespace Evolution\DataModel\Resource;

class Recipe
{

    /**
     * @var Count[]
     */
    private array $products = [];

    /**
     * @var Count[]
     */
    private array $materials = [];

    public function __construct(Count|array $product)
    {
        $this->products = is_array($product) ? $product : [$product];
    }

    /**
     * @param Count $material
     * @return $this
     */
    public function addMaterial(Count $material): static
    {
        $this->materials[$material->resource->name] = $material;
        return $this;
    }

    /**
     * @return Count[]
     */
    public function products(): array
    {
        return $this->products;
    }

    /**
     * @return Count[]
     */
    public function materials(): array
    {
        return $this->materials;
    }

}
