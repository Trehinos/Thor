<?php

namespace Thor\Structures;

class Container extends Item implements ContainerInterface
{

    /**
     * @var ContainerInterface[]|ItemInterface[]
     */
    private array $data = [];

    public function __construct(string $key) {
        parent::__construct($key, null);
    }

    public function getValue(): array
    {
        return $this->data;
    }

    public function set(ContainerInterface|ItemInterface $child): static
    {
        $this->data[$child->getKey()] = $child;
        return $this;
    }

    public function get(string $key): ContainerInterface|ItemInterface|null
    {
        return $this->data[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param callable   $operation (string $key, ContainerInterface|ItemInterface|null $item): mixed
     * @param array|null $keys
     *
     * @return array
     */
    public function each(callable $operation, ?array $keys = null): array
    {
        return array_map(
            function (string $key, ContainerInterface|ItemInterface|null $value) use ($operation, $keys) {
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
        $this->each(
            function (string $key, ContainerInterface|ItemInterface|null $value) use ($container) {
                $container->set($value);
            }
        );
        return $this;
    }
}
