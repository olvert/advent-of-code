<?php

namespace Advent\Year2022;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Opponent
 * A — Rock
 * B — Paper
 * C — Scissor
 * 
 * Player
 * X — Rock (1)
 * Y — Paper (2)
 * Z — Scissor (3)
 */
class Day2 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->map(fn (string $round) => self::roundScore($round) + self::shapeScore($round))
            ->sum();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');
    }

    private static function roundScore(string $round): int
    {
        return match ($round) {
            'A X' => 3,
            'A Y' => 6,
            'A Z' => 0,
            'B X' => 0,
            'B Y' => 3,
            'B Z' => 6,
            'C X' => 6,
            'C Y' => 0,
            'C Z' => 3,
        };
    }

    private static function shapeScore(string $round): int
    {
        return match (Str::of($round)->charAt(2)) {
            'X' => 1,
            'Y' => 2,
            'Z' => 3,
        };
    }
}
