<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Spatie\CollectionMacros\Macros\Transpose;

class Day3 extends Day
{
    public static function solveA()
    {
        Collection::macro('transpose', (new Transpose())());

        $input = InputLoader::load(__DIR__ . '/input.txt');

        $transposedInput = collect($input)
            ->map(fn ($string) => str_split($string))
            ->transpose();

        $gammaRate = $transposedInput
            ->map(fn ($c) => static::mostCommonValue($c))
            ->join('');

        $epsilonRate = $transposedInput
            ->map(fn ($c) => static::leastCommonValue($c))
            ->join('');

        return bindec($gammaRate) * bindec($epsilonRate);
    }

    public static function solveB()
    {
        Collection::macro('transpose', (new Transpose())());

        $input = InputLoader::load(__DIR__ . '/input.txt');
        $parsedInput = collect($input)->map(fn ($string) => collect(str_split($string))->map(fn ($s) => intval($s)));

        $generatorRating = static::getGeneratorRating($parsedInput);
        $scrubberRating = static::getScrubberRating($parsedInput);

        return bindec($generatorRating) * bindec($scrubberRating);
    }

    private static function getGeneratorRating(Collection $input, int $bitPosition = 0): string
    {
        if ($input->count() === 1) {
            return $input->first()->join('');
        }

        $mostCommonValue = static::mostCommonValue($input->transpose()->get($bitPosition));

        return static::getGeneratorRating(
            $input->filter(fn ($number) => $number->get($bitPosition) === $mostCommonValue),
            $bitPosition + 1
        );
    }

    private static function getScrubberRating(Collection $input, int $bitPosition = 0): string
    {
        if ($input->count() === 1) {
            return $input->first()->join('');
        }

        $leastCommonValue = static::leastCommonValue($input->transpose()->get($bitPosition));

        return static::getScrubberRating(
            $input->filter(fn ($number) => $number->get($bitPosition) === $leastCommonValue),
            $bitPosition + 1
        );
    }

    private static function mostCommonValue(Collection $c): int
    {
        return $c->sum() >= ($c->count() / 2) ? 1 : 0;
    }

    private static function leastCommonValue(Collection $c): int
    {
        return $c->sum() >= ($c->count() / 2) ? 0 : 1;
    }
}
