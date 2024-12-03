<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Day3 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::loadString(__DIR__ . '/input.txt');

        $instructions = Str::of($input)->matchAll('/mul\(\d+,\d+\)/');

        return static::multiplyAndSumInstructions($instructions);
    }

    public static function solveB()
    {
        $input = InputLoader::loadString(__DIR__ . '/input.txt');

        [, $instructions] = Str::of($input)->matchAll('/mul\(\d+,\d+\)|do\(\)|don\'t\(\)/')
            ->reduce(fn (array $carry, string $item) => match ($item) {
                'do()' => [true, $carry[1]],
                'don\'t()' => [false, $carry[1]],
                default => $carry[0] ? [$carry[0], [...$carry[1], $item]] : $carry
            }, [true, []]);

        return static::multiplyAndSumInstructions($instructions);
    }

    private static function multiplyAndSumInstructions(Collection|array $instructions): int
    {
        return Collection::wrap($instructions)
            ->map(fn (string $instruction) => Str::of($instruction)->matchAll('/\d+/'))
            ->map(fn (Collection $numbers) => $numbers->get(0) * $numbers->get(1))
            ->sum();
    }
}
