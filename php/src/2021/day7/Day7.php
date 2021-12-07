<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day7 extends Day
{
    /**
     * @see https://math.stackexchange.com/questions/3092033/find-a-number-having-minimum-sum-of-distances-between-a-set-of-numbers
     */
    public static function solveA()
    {
        $positions = static::parseInput();
        
        $median = $positions->median();

        return $positions->map(fn ($pos) => abs($pos - $median))->sum();
    }

    public static function solveB()
    {
        $positions = static::parseInput();

        $min = $positions->min();
        $max = $positions->max();

        $range = collect(range($min, $max));

        return $range->map(fn ($alignPos) => static::totalCost($positions, $alignPos))->min();
    }

    private static function totalCost(Collection $positions, int $alignPos): int
    {
        return $positions->map(fn ($pos) => static::movementCost(abs($pos - $alignPos)))->sum();
    }

    private static function movementCost(int $distance): int
    {
        static $cache = [
            0 => 0
        ];

        if (! isset($cache[$distance])) {
            $cache[$distance] = static::movementCost($distance - 1) + $distance;
        }

        return $cache[$distance];
    }

    private static function parseInput(): Collection
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect(explode(',', $input[0]))->map(fn ($pos) => intval($pos))->sort();
    }
}
