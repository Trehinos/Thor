<?php

namespace Thor\Structures;

interface ContainerInterface extends ItemInterface
{

    public function set(ContainerInterface|ItemInterface $child): static;
    public function get(string $key): ContainerInterface|ItemInterface|null;
    public function has(string $key): bool;
    public function each(callable $operation, ?array $keys = null);
    public function copy(ContainerInterface $container): static;

}
