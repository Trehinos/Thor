<?php

namespace Evolution\Db;

/**
 * @implements Hydratable
 */
trait CanHydrate
{

    private static function normalize(string $name): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_${1}', $name));
    }

    private static function getColumns(?self $object = null): array
    {
        $columns = [];
        $rc = new \ReflectionClass(static::class);
        foreach ($rc->getProperties() as $property) {
            $columns[self::normalize($property->getName())] = [
                'property' => $property,
                'value '   => $object === null ? null : $property->getValue($object)
            ];
        }
        return $columns;
    }

    public static function hydrates(array $pdoArray): static
    {
        $o = new static();
        foreach (self::getColumns() as $column => ['property' => $property, 'value' => $value]) {
            $property->setValue($o, $pdoArray[$column]);
        }
        return $o;
    }

    public function toPdoArray(): array
    {
        $c = self::getColumns($this);
        return array_combine(
            array_keys($c),
            array_map(fn(array $info) => $info['value'], $c)
        );
    }
}
