<?php

namespace Advent\Year2023;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Day4 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->map(self::numOfWinningNumbersForCard(...))
            ->filter()
            ->map(fn ($numOfMatches) => 2 ** ($numOfMatches - 1))
            ->sum();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $originalCards = collect($input)->map(self::numOfWinningNumbersForCard(...));

        return $originalCards
            ->keys()
            ->map(fn ($cardNumber) => self::getValueForCard($cardNumber, $originalCards))
            ->sum();
    }

    private static function getValueForCard(int $cardNumber, Collection $originalCards): int
    {
        static $cache = [];

        if (isset($cache[$cardNumber])) {
            dump('cache hit for: ' . $cardNumber);
            return $cache[$cardNumber];
        }

        dump('cache miss for: ' . $cardNumber);

        $numOfWonCards = $originalCards->get($cardNumber);

        $cardsToProcess = $numOfWonCards === 0
            ? collect()
            : collect()
                ->range($cardNumber + 1, $cardNumber + $numOfWonCards)
                ->filter(fn (int $number) => $number < $originalCards->count());

        return $cache[$cardNumber] = 1 + $cardsToProcess
            ->map(fn ($number) => self::getValueForCard($number, $originalCards))
            ->sum();
    }

    private static function numOfWinningNumbersForCard(string $card): int
    {
        return Str::of($card)
            ->after(':')
            ->before('|')
            ->matchAll('/\d+/')
            ->intersect(Str::of($card)->after('|')->matchAll('/\d+/'))
            ->count();
    }
}
