<?php

namespace Thor\Ngine;

class Module extends Node
{

    private ?Node $currentPage;


    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    /**
     * @template T
     * @template-extends Node
     *
     * @param class-string<T> $type
     *
     * @return array<T>|T
     */
    public function filter(string $type, ?string $name = null): array|Node
    {
        $output = [];
        foreach ($this->children as $child) {
            if ($child instanceof $type) {
                if ($name !== null && $child->name === $name) {
                    return $child;
                }
                $output[$child->name] = $child;
            }
        }
        return $output;
    }

    public function draw()
    {
    }

    public function action(string $action)
    {

    }

}
