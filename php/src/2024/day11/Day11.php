<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Day11 extends Day
{
    public static function solveA()
    {
        return static::solve(25);
    }

    public static function solveB()
    {
        return static::solve(75);
    }

    private static function solve(int $numOfBlinks): int
    {
        $input = InputLoader::loadString(__DIR__ . '/input.txt');

        $cache = Collection::times($numOfBlinks + 1, fn () => collect());

        return static::parseInput($input)
            ->map(fn (int $stone) => static::blink($stone, $numOfBlinks, $cache))
            ->sum();
    }

    private static function blink(int $stone, int $times, Collection $cache)
    {
        return $cache[$times][$stone] ??= match ($times) {
            0 => 1,
            default => static::evolveStone($stone)
                ->map(fn (int $evolvedStone) => static::blink($evolvedStone, $times - 1, $cache))
                ->flatten()
                ->sum(),
        };
    }

    private static function evolveStone(int $stone): Collection
    {
        $evolvedStone = match (true) {
            $stone === 0 => 1,
            Str::of($stone)->length() % 2 === 0 => static::splitStone($stone),
            default => $stone * 2024,
        };

        return Collection::wrap($evolvedStone);
    }

    private static function splitStone(int $stone)
    {
        $stone = Str::of($stone);
        $index = $stone->length() / 2;

        return [
            $stone->substr(0, $index)->toInteger(),
            $stone->substr($index)->toInteger(),
        ];
    }

    private static function parseInput(string $input): Collection
    {
        return collect(explode(' ', $input))->map(fn (string $stone) => intval($stone));
    }
}
