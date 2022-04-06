<?php

namespace Thor\Structures\Collection;

use Iterator;
use Countable;
use ArrayAccess;
use JetBrains\PhpStorm\ArrayShape;

class Collection implements ArrayAccess, Iterator, Countable
{

    public function __construct(private array $data = [])
    {
    }

    #[ArrayShape(['data' => "array"])]
    public function __serialize(): array
    {
        return ['data' => $this->data];
    }

    public function __unserialize(array $data): void
    {
        $this->data = $data['data'];
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

    public static function range(string|int|float $start, string|int|float $end, int|float $step = 1): static
    {
        return new static(range($start, $end, $step));
    }

    public function toArray(): array
    {
        return $this->data;
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
        if (null === $offset) {
            $this->data[] = $value;
            return;
        }
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->data[$offset] = null;
        unset($this->data[$offset]);
    }

    public function count(bool $recursive = false): int
    {
        return count($this->data, $recursive ? COUNT_RECURSIVE : COUNT_NORMAL);
    }

    public function current(): mixed
    {
        return current($this->data);
    }

    public function next(): void
    {
        next($this->data);
    }

    public function key(): string|int|null
    {
        return key($this->data);
    }

    public function valid(): bool
    {
        return $this->offsetExists($this->key());
    }

    public function rewind(): void
    {
        reset($this->data);
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

    public function filter(?callable $filterFunction = null, FilterMode $mode = FilterMode::USE_VALUE): static
    {
        return new static(array_filter($this->data, $filterFunction, $mode->value));
    }

    public function flip(): static
    {
        return new static(array_flip($this->data));
    }

    public function keys(mixed $searchValue = null, bool $strict = false): static
    {
        return new static(array_keys($this->data, $searchValue, $strict));
    }

    public function map(?callable $function, self ...$arrays): static
    {
        return $this->forAllArrays(fn(array $data) => array_map($function, $this->data, ...$data), ...$arrays);
    }

    public function merge(self ...$arrays): static
    {
        return $this->forAllArrays(fn(array $data) => array_merge($this->data, ...$data), ...$arrays);
    }

    public function mergeRecursive(self ...$arrays): static
    {
        return $this->forAllArrays(fn(array $data) => array_merge_recursive($this->data, ...$data), ...$arrays);
    }

    public function pad(int $length, mixed $value): static
    {
        return new static(array_pad($this->data, $length, $value));
    }

    public function replace(self ...$replacements): static
    {
        return $this->forAllArrays(fn(array $data) => array_replace($this->data, ...$data), ...$replacements);
    }

    public function replaceRecursive(self ...$replacements): static
    {
        return $this->forAllArrays(fn(array $data) => array_replace_recursive($this->data, ...$data), ...$replacements);
    }

    public function reverse(bool $preserveKeys = false): static
    {
        return new static(array_reverse($this->data, $preserveKeys));
    }

    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): static
    {
        return new static(array_slice($this->data, $offset, $length, $preserveKeys));
    }

    public function splice(int $offset, ?int $length = null, self $replacement = new self()): static
    {
        return new static(array_splice($this->data, $offset, $length, $replacement->data));
    }

    public function unique(UniqueSortFlag $sortFlag = UniqueSortFlag::REGULAR): static
    {
        return new static(array_unique($this->data, $sortFlag->value));
    }

    public function values(): static
    {
        return new static(array_values($this->data));
    }

    public function diff(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff($ret, $array), ...$arrays);
    }

    public function uDiff(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_udiff($ret, $array, $compare), ...$arrays);
    }

    public function diffAssoc(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff_assoc($ret, $array), ...$arrays);
    }

    public function diffUAssoc(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff_uassoc($ret, $array, $compare), ...$arrays);
    }

    public function uDiffAssoc(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_udiff_assoc($ret, $array, $compare), ...$arrays);
    }

    public function uDiffUAssoc(callable $compareKey, callable $compareValue, self ...$arrays): static
    {
        return $this->forEachArray(
            fn($ret, $array) => array_udiff_uassoc($ret, $array, $compareValue, $compareKey),
            ...$arrays
        );
    }

    public function diffKey(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff_key($ret, $array), ...$arrays);
    }

    public function diffUKey(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_diff_ukey($ret, $array, $compare), ...$arrays);
    }

    public function intersect(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect($ret, $array), ...$arrays);
    }

    public function uIntersect(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_uintersect($ret, $array, $compare), ...$arrays);
    }

    public function intersectAssoc(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect_assoc($ret, $array), ...$arrays);
    }

    public function uIntersectAssoc(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_uintersect_assoc($ret, $array, $compare), ...$arrays);
    }

    public function intersectKey(self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect_key($ret, $array), ...$arrays);
    }

    public function intersectUAssoc(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect_uassoc($ret, $array, $compare), ...$arrays);
    }

    public function uIntersectUAssoc(callable $compareKey, callable $compareValue, self ...$arrays): static
    {
        return $this->forEachArray(
            fn($ret, $array) => array_uintersect_uassoc($ret, $array, $compareValue, $compareKey),
            ...$arrays
        );
    }

    public function intersectUKey(callable $compare, self ...$arrays): static
    {
        return $this->forEachArray(fn($ret, $array) => array_intersect_ukey($ret, $array, $compare), ...$arrays);
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

    public function search(mixed $needle, bool $strict = false): int|string|false
    {
        return array_search($needle, $this->data, $strict);
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

    public function sum(): int|float
    {
        return array_sum($this->data);
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->data, $callback, $initial);
    }

    public function in(mixed $needle, bool $strict = false): bool
    {
        return in_array($needle, $this->data, $strict);
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

    public function shift(): mixed
    {
        return array_shift($this->data);
    }

    public function unshift(mixed ...$values): int
    {
        return array_unshift($this->data, ...$values);
    }

    public function walk(callable $callback, mixed $arg = null): bool
    {
        return array_walk($this->data, $callback, $arg);
    }

    public function shuffle(): bool
    {
        return shuffle($this->data);
    }

    /**
     * @param SortOrder $order
     * @param SortFlag  $flag
     * @param bool      $preserveKeys
     *
     * @return bool
     *
     * @see arsort = (DESC, *, true)
     * @see asort = (ASC, *, true)
     * @see sort = (ASC, *, false)
     * @see rsort = (DESC, *, false)
     */
    public function sort(
        SortOrder $order = SortOrder::ASC,
        SortFlag $flag = SortFlag::REGULAR,
        bool $preserveKeys = false
    ): bool {
        return match ($order) {
            SortOrder::ASC => match ($preserveKeys) {
                true => asort($this->data, $flag->value),
                false => sort($this->data, $flag->value)
            },
            SortOrder::DESC => match ($preserveKeys) {
                true => arsort($this->data, $flag->value),
                false => rsort($this->data, $flag->value)
            }
        };
    }

    public function kSort(SortOrder $order = SortOrder::ASC, SortFlag $flag = SortFlag::REGULAR): bool
    {
        return match ($order) {
            SortOrder::ASC => ksort($this->data, $flag->value),
            SortOrder::DESC => krsort($this->data, $flag->value)
        };
    }

    public function natSort(bool $caseInsensitive = false): bool
    {
        return match ($caseInsensitive) {
            true => natcasesort($this->data),
            false => natsort($this->data)
        };
    }

    public function uSort(callable $compare, bool $sortKeys = false, bool $preserveKeys = false): bool
    {
        return match ($sortKeys) {
            true => uksort($this->data, $compare),
            false => match ($preserveKeys) {
                true => uasort($this->data, $compare),
                false => usort($this->data, $compare)
            }
        };
    }
}
