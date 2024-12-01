<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day1 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        [$leftList, $rightList] = static::parseLists($input);

        return $leftList
            ->sort()
            ->zip($rightList->sort())
            ->map(fn (Collection $pair) => abs($pair[0] - $pair[1]))
            ->sum();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        [$leftList, $rightList] = static::parseLists($input);

        $occurrences = $rightList
            ->groupBy(fn (int $item) => $item)
            ->map(fn (Collection $group) => $group->count());

        return $leftList
            ->map(fn (int $item) => $item * $occurrences->get($item, 0))
            ->sum();
    }

    private static function parseLists(array $input)
    {
        return collect($input)
            ->map(fn (string $row) => explode('   ', $row))
            ->collapse()
            ->partition(fn (int $item, int $index) => $index % 2 === 0);
    }
}
