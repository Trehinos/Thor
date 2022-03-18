<?php

namespace Thor\Php;

use ArrayAccess;
use phpDocumentor\Reflection\Types\Static_;

class Collection implements ArrayAccess
{

    public function __construct(private array $data = [])
    {
    }

    public static function combine(self $keys, self $values): static
    {
        return new static(array_combine($keys->data, $values->data));
    }

    public static function fillKeys(self $keys, mixed $value): static
    {
        return new static(array_fill_keys($keys->data, $value));
    }

    public static function fill(int $start = 0, int $count = 1, mixed $value = ''): static
    {
        return new static(array_fill($start, $count, $value));
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->data[$offset] = null;
        unset($this->data[$offset]);
    }

    ////////////////////////////////////////////////////////////////////////////
    /// Collection returns

    public function changeKeyCase(KeyCase $case): static
    {
        return new static(array_change_key_case($this->data, $case->value));
    }

    public function chunk(int $length, bool $preserveKeys = false): static
    {
        return new static(array_chunk($this->data, $length, $preserveKeys));
    }

    public function column(int|string|null $columnKey, int|string|null $indexKey = null): static
    {
        return new static(array_column($this->data, $columnKey, $indexKey));
    }

    public function countValues(): static
    {
        return new static(array_count_values($this->data));
    }

    public function diff(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff($ret, $array), ...$arrays);
    }

    public function diffAssoc(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff_assoc($ret, $array), ...$arrays);
    }

    public function diffUAssoc(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff_uassoc($ret, $array, $compare), ...$arrays);
    }

    public function diffKey(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff_key($ret, $array), ...$arrays);
    }

    public function diffUKey(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff_ukey($ret, $array, $compare), ...$arrays);
    }

    public function filter(?callable $filterFunction = null, FilterMode $mode = FilterMode::USE_VALUE): static
    {
        return new static(array_filter($this->data, $filterFunction, $mode->value));
    }

    public function flip(): static
    {
        return new static(array_flip($this->data));
    }

    public function intersect(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect($ret, $array), ...$arrays);
    }

    public function intersectAssoc(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect_assoc($ret, $array), ...$arrays);
    }

    public function intersectKey(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect_key($ret, $array), ...$arrays);
    }

    public function intersectUAssoc(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect_uassoc($ret, $array, $compare), ...$arrays);
    }

    public function intersectUKey(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect_ukey($ret, $array, $compare), ...$arrays);
    }

    public function keys(mixed $searchValue = null, bool $strict = false): static
    {
        return new static(array_keys($this->data, $searchValue, $strict));
    }

    public function map(?callable $function, self ...$arrays): static
    {
        return $this->forAllArrays(fn (array $data) => array_map($function, $this->data, ...$data), ...$arrays);
    }

    public function merge(self ...$arrays): static
    {
        return $this->forAllArrays(fn (array $data) => array_merge($this->data, ...$data), ...$arrays);
    }

    public function mergeRecursive(self ...$arrays): static
    {
        return $this->forAllArrays(fn (array $data) => array_merge_recursive($this->data, ...$data), ...$arrays);
    }

    public function pad(int $length, mixed $value): static
    {
        return new static(array_pad($this->data, $length, $value));
    }

    public function replace(self ...$replacements): static
    {
        return $this->forAllArrays(fn (array $data) => array_replace($this->data, ...$data), ...$replacements);
    }

    private function forAllArrays(callable $function, self ...$arrays): static
    {
        $data = [];
        foreach ($arrays as $array) {
            $data[] = $array->data;
        }
        return new static($function($data));
    }

    private function forEachArray(callable $function, self ...$arrays): static
    {
        $ret = $this->data;
        foreach ($arrays as $array) {
            $ret = $function($ret, $array);
        }
        return new static($ret);
    }

    ////////////////////////////////////////////////////////////////////////////
    /// Atomic returns

    public function rand(int $num = 1): int|string|static
    {
        $result = array_rand($this->data, $num);
        if (is_array($result)) {
            return new static($result);
        }
        return $result;
    }

    public function isList(): bool
    {
        return array_is_list($this->data);
    }

    public function keyExists(int|string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function keyFirst(): int|string|null
    {
        return array_key_first($this->data);
    }

    public function keyLast(): int|string|null
    {
        return array_key_last($this->data);
    }

    public function product(): int|float
    {
        return array_product($this->data);
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->data, $callback, $initial);
    }

    ////////////////////////////////////////////////////////////////////////////
    /// Mutators

    public function multiSort(SortOrder $sortOrder = SortOrder::ASC, SortFlag $sortFlag = SortFlag::REGULAR): void
    {
        array_multisort($this->data, ($sortOrder->value), ($sortFlag->value));
    }

    public function pop(): mixed
    {
        return array_pop($this->data);
    }

    public function push(mixed ...$values): int
    {
        return array_push($this->data, ...$values);
    }

}
