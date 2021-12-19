<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day8 extends Day
{
    public static function solveA()
    {
        $entries = static::parseInput();

        return $entries
            ->map(fn ($entry) => $entry['output'])
            ->collapse()
            ->filter(fn ($value) => in_array($value->count(), [2, 3, 4, 7]))
            ->count();
    }

    public static function solveB()
    {
        $entries = static::parseInput();

        return $entries
            ->map(fn ($entry) => static::parseOutputValue($entry))
            ->sum();
    }

    private static function parseOutputValue(array $entry): int
    {
        $mapping = static::patternsToDigitMapping($entry['patterns']);

        $outputValue = $entry['output']
            ->map(fn ($pattern) => $mapping[$pattern->sort()->join('')])
            ->join('');

        return intval($outputValue);
    }

    private static function patternsToDigitMapping(Collection $patterns): Collection
    {
        /* Unique number of segments (1, 3, 7, 8) */
        $one = $patterns->first(fn ($p) => $p->count() === 2);
        $seven = $patterns->first(fn ($p) => $p->count() === 3);
        $four = $patterns->first(fn ($p) => $p->count() === 4);
        $eight = $patterns->first(fn ($p) => $p->count() === 7);

        /* 6 segments (0, 6, 9) */
        $six = $patterns->first(fn ($p) => $p->count() === 6 && $one->diff($p)->count() === 1);
        $zero = $patterns->first(fn ($p) => $p->count() === 6 && $p !== $six && $four->diff($p)->count() === 1);
        $nine = $patterns->first(fn ($p) => $p->count() === 6 && $p !== $six && $p !== $zero);

        /* 5 segments (2, 3, 5) */
        $three = $patterns->first(fn ($p) => $p->count() === 5 && $one->diff($p)->count() === 0);
        $two = $patterns->first(fn ($p) => $p->count() === 5 && $p !== $three && $nine->diff($p)->count() === 2);
        $five = $patterns->first(fn ($p) => $p->count() === 5 && $p !== $three && $p !== $two);

        return collect([
            '0' => $zero,
            '1' => $one,
            '2' => $two,
            '3' => $three,
            '4' => $four,
            '5' => $five,
            '6' => $six,
            '7' => $seven,
            '8' => $eight,
            '9' => $nine,
        ])->mapWithKeys(fn ($pattern, $key) => [$pattern->sort()->join('') => $key]);
    }

    private static function parseInput(): Collection
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)->map(function ($row) {
            [$patterns, $output] = explode(' | ', $row);

            return [
                'patterns' => collect(explode(' ', $patterns))->map(fn ($value) => collect(str_split($value))),
                'output' => collect(explode(' ', $output))->map(fn ($value) => collect(str_split($value))),
            ];
        });
    }
}
