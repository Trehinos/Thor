<?php

namespace Thor\Structures;

interface ContainerInterface extends ItemInterface
{

    public function setItem(ItemInterface $child): static;
    public function getItem(string $key): ?ItemInterface;
    public function removeItem(string $key): bool;
    public function hasItem(string $key): bool;
    public function eachItem(callable $operation, ?array $keys = null);
    public function copy(ContainerInterface $container): static;

}
