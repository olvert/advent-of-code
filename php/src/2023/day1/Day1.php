<?php

namespace Advent\Year2023;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Day1 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->map(fn (string $row) => Str::of($row)->matchAll('/\d/'))
            ->map(fn (Collection $numbers) => "{$numbers->first()}{$numbers->last()}")
            ->sum();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->map(fn (string $row) => Str::of($row)->matchAll('/(?=(\d|one|two|three|four|five|six|seven|eight|nine))/'))
            ->map(fn (Collection $numbers) => $numbers->map(fn (string $number) => match ($number) {
                'one' => 1,
                'two' => 2,
                'three' => 3,
                'four' => 4,
                'five' => 5,
                'six' => 6,
                'seven' => 7,
                'eight' => 8,
                'nine' => 9,
                default => $number,
            }))
            ->map(fn (Collection $numbers) => "{$numbers->first()}{$numbers->last()}")
            ->sum();
    }
}
