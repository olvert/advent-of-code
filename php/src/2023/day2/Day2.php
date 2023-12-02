<?php

namespace Advent\Year2023;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Day2 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->mapWithKeys(self::parseGame(...))
            ->filter(self::gameIsPossible(...))
            ->keys()
            ->sum();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->mapWithKeys(self::parseGame(...))
            ->map(self::findColorMinimumsForGame(...))
            ->map(fn (Collection $gameColorMinimums) => $gameColorMinimums
                ->values()
                ->reduce(fn (int $product, int $factor) => $product * $factor, 1))
            ->sum();
    }

    private static function parseGame(string $game, int $index): array
    {
        $parsedGame = Str::of($game)
            ->after(': ')
            ->explode('; ')
            ->map(fn (string $set) => Str::of($set)
                ->explode(', ')
                ->mapWithKeys(fn ($color) => [
                    Str::of($color)->match('/[a-z]+/')->toString() => Str::of($color)->match('/\d+/')->toString(),
                ])
            );

        return [$index + 1 => $parsedGame];
    }

    private static function gameIsPossible(Collection $game)
    {
        $limits = [
            'red' => 12,
            'green' => 13,
            'blue' => 14,
        ];

        return $game->every(fn (Collection $set) => $set->every(fn (int $count, string $color) => $limits[$color] >= $count));
    }

    private static function findColorMinimumsForGame(Collection $game): Collection
    {
        return $game->reduce(
            fn (Collection $minimums, Collection $set) => collect(['red', 'green', 'blue'])
                ->mapWithKeys(fn (string $color) => [
                    $color => max(1, $minimums->get($color), $set->get($color))
                ]),
            collect()
        );
    }
}
