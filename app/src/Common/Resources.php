<?php

namespace Evolution\Common;

use Evolution\DataModel\Resource\Count;
use Evolution\DataModel\Resource\Resource;

class Resources
{

    private static array $data = [];

    public static function count(string $resourceName, float $count): Count
    {
        $resources = self::allResources();
        return new Count($resources[$resourceName], $count);
    }

    public static function countMax(string $resourceName, float $count, float $max): Count
    {
        $resources = self::allResources();
        return new Count($resources[$resourceName], $count, $max);
    }

    public static function counts(array $counts): array
    {
        return array_map(
            fn(string $name, array|float $count) => is_array($count)
                ? self::countMax($name, $count['count'] ?? 0.0, $count['max'] ?? null)
                : self::count($name, $count),
            array_keys($counts),
            array_values($counts),
        );
    }

    /**
     * @return Resource[]
     */
    public static function allResources(): array
    {
        return self::$data['all'] ??= [
            ...self::peopleResources(),
            ...self::cityResources(),
            ...self::baseResources(),
            ...self::materials()
        ];
    }

    public static function get(string $name): ?Resource
    {
        $all = self::allResources();
        return $all[$name] ?? null;
    }

    /**
     * @return Resource[]
     */
    public static function peopleResources(): array
    {
        return self::$data['people'] ??= [
            'faith'     => new Resource('Foi', icon: 'praying-hands', color: '#ddd', flags: Resource::PRIMARY),
            'culture'   => new Resource('Culture', icon: 'books', color: '#b7d', flags: Resource::PRIMARY),
            'technique' => new Resource('Technique', icon: 'tools', color: '#99d', flags: Resource::PRIMARY),
            'science'   => new Resource('Science', icon: 'flask', color: '#0c9', flags: Resource::PRIMARY),
            'money'     => new Resource('Monaie', icon: 'coins', color: '#ac4', flags: Resource::PRIMARY),
        ];
    }

    /**
     * @return Count[]
     */
    public static function peopleCosts(
        float $faith = 0.0,
        float $culture = 0.0,
        float $technique = 0.0,
        float $science = 0.0,
        float $money = 0.0
    ): array {
        return [
            'faith'     => self::count('faith', $faith),
            'culture'   => self::count('culture', $culture),
            'technique' => self::count('technique', $technique),
            'science'   => self::count('science', $science),
            'money'     => self::count('money', $money),
        ];
    }

    /**
     * @return Resource[]
     */
    public static function cityResources(): array
    {
        return self::$data['city'] ??= [
            'food'        => new Resource('Nourriture', icon: 'wheat', color: '#ac4', flags: Resource::PRIMARY),
            'wealth'      => new Resource('Richesse', icon: 'wheat', color: '#ac4', flags: Resource::PRIMARY),
            'production'  => new Resource('Production', icon: 'books', color: '#9d4', flags: Resource::PRIMARY),
            'health'      => new Resource('Santé', icon: 'wheat', color: '#ac4', flags: Resource::PRIMARY),
            'happiness'   => new Resource('Bohneur', icon: 'wheat', color: '#ac4', flags: Resource::PRIMARY),
            'electricity' => new Resource('Electricité', icon: 'wheat', color: '#ac4', flags: Resource::PRIMARY),
        ];
    }

    /**
     * @return Resource[]
     */
    public static function baseResources(): array
    {
        return self::$data['base'] ??= [
            'wood'      => new Resource('Bois', Units::weight(), 'tree', '#aaa', Resource::COLLECTABLE),
            'stone'     => new Resource('Pierre', Units::weight(), 'mountains', '#aaa', Resource::COLLECTABLE),
            'game'      => new Resource('Gibier', icon: 'deer', color: '#aaa', flags: Resource::COLLECTABLE),
            'livestock' => new Resource('Bétail', icon: 'deer', color: '#aaa', flags: Resource::COLLECTABLE),
            'water'     => new Resource('Eau', Units::volume(), 'tint', '#aaa', Resource::COLLECTABLE),
            'copper'    => new Resource('Cuivre', Units::weight(), '', '#aaa', Resource::COLLECTABLE),
            'cotton'    => new Resource('Coton', Units::weight(), '', '#aaa', Resource::COLLECTABLE),
        ];
    }

    /**
     * @return Resource[]
     */
    public static function materials(): array
    {
        return self::$data['materials'] ??= [
            'wood_plank' => new Resource('Planche en bois', null, 'horizontal-rule', '#aaa', Resource::PRODUCT),
            'wood_plate' => new Resource('Plaque de bois', null, 'square', '#aaa', Resource::PRODUCT),
            'wood_rod'   => new Resource('Tige de bois', null, 'line-columns', '#aaa', Resource::PRODUCT),
            'wood_beam'  => new Resource('Poutre de bois', null, 'grip-lines', '#aaa', Resource::PRODUCT),
            'skin'       => new Resource('Peau', Units::weight(), 'scroll', '#aaa', Resource::PRODUCT),
            'leather'    => new Resource('Cuir', null, 'scroll', '#aaa', Resource::PRODUCT),
        ];
    }

}
