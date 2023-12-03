<?php

namespace Advent\Year2022;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day1 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->chunkWhile(fn (string $row) => ! empty($row))
            ->map(fn (Collection $calories) => $calories->filter()->sum())
            ->max();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->chunkWhile(fn (string $row) => ! empty($row))
            ->map(fn (Collection $calories) => $calories->filter()->sum())
            ->sortDesc()
            ->take(3)
            ->sum();
    }
}
