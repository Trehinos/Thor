<?php

namespace Evolution\Common;

use Evolution\DataModel\City\City;
use Evolution\DataModel\Player\Advance;
use Evolution\DataModel\Player\Nation;
use Evolution\DataModel\Player\Player;
use Evolution\DataModel\Player\Unlock;
use Evolution\DataModel\Resource\Count;
use Evolution\DataModel\Resource\Recipe;

final class StaticFactory
{

    private static array $data = [];

    public static function createTestCity(): City
    {
        return new City('Ville de test');
    }

    public static function createTestPlayer(): Player
    {
        return new Player('Razelus', new Nation('Razie', cities: [self::createTestCity()]));
    }

    /**
     * @return array<string, array>
     */
    public static function gameData(): array
    {
        return [
            'resources' => Resources::allResources(),
            'recipes'   => self::recipes(),
        ];
    }

    public static function basicTree(): array
    {
        return self::$data['basic_tree'] ??= [
            'settlement' => new Advance(
                'Sédentarisation',
                'Votre peuple s\'installe sur ses propres terres et apprend à prospérer.',
                Resources::peopleCosts(),
                [
                    new Unlock(Resources::get('wood')),
                    new Unlock(Resources::get('stone')),
                    new Unlock(Resources::get('food')),
                ]
            )
        ];
    }

    /**
     * @return array<string, Recipe>
     */
    public static function recipes(): array
    {
        return self::$data['recipes'] ??= [
            'hunt_game'  => self::createRecipe(
                Resources::counts(['skin' => 2, 'food' => 35]),
                Resources::counts(['game' => 1])
            ),
            'leather'    => self::createRecipe(
                Resources::count('leather', 1),
                Resources::counts(['skin' => 2])
            ),
            'wood_plank' => self::createRecipe(
                Resources::count('wood_plank', 1),
                Resources::counts(['wood' => 6])
            ),
            'wood_plate' => self::createRecipe(
                Resources::count('wood_plate', 1),
                Resources::counts(['wood' => 6])
            ),
            'wood_rod'   => self::createRecipe(
                Resources::count('wood_rod', 10),
                Resources::counts(['wood' => 5])
            ),
            'wood_beam'  => self::createRecipe(
                Resources::count('wood_beam', 2),
                Resources::counts(['wood' => 40])
            ),
        ];
    }

    public static function createRecipe(Count|array $product, array $materials): Recipe
    {
        $recipe = new Recipe($product);
        foreach ($materials as $material) {
            $recipe->addMaterial($material);
        }
        return $recipe;
    }

}
