<?php

namespace Thor\Structures;

class Container extends Item implements ContainerInterface
{

    /**
     * @var ItemInterface[]
     */
    private array $data = [];

    public function __construct(string $key)
    {
        parent::__construct($key, null);
    }

    public function getValue(): array
    {
        return $this->data;
    }

    public function setItem(ItemInterface $child): static
    {
        $this->data[$child->getKey()] = $child;
        return $this;
    }

    public function getItem(string $key): ?ItemInterface
    {
        return $this->data[$key] ?? null;
    }

    public function hasItem(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param callable $operation (string $key, ContainerInterface|ItemInterface|null $item): mixed
     * @param array|null $keys
     *
     * @return array
     */
    public function eachItem(callable $operation, ?array $keys = null): array
    {
        return array_map(
            function (string $key, ?ItemInterface $value) use ($operation, $keys) {
                if ($keys !== null && !in_array($key, $keys)) {
                    return $value;
                }
                return $operation($key, $value);
            },
            array_keys($this->data),
            array_values($this->data)
        );
    }

    public function copy(ContainerInterface $container): static
    {
        $this->eachItem(
            function (string $key, ?ItemInterface $value) use ($container) {
                $container->setItem($value);
            }
        );
        return $this;
    }

    public function removeItem(string $key): bool
    {
        if (!$this->hasItem($key)) {
            return false;
        }

        $this->data[$key] = null;
        unset($this->data[$key]);
        return true;
    }
}
