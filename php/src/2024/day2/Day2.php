<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class Day2 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return static::parseReports($input)
            ->filter(static::isValidReport(...))
            ->count();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return static::parseReports($input)
            ->filter(fn (Collection $report) => static::createCombinations($report)->some(static::isValidReport(...)))
            ->count();
    }

    private static function isValidReport(Collection $report): bool
    {
        return (static::isIncreasing($report) || static::isDecreasing($report)) && static::hasValidSteps($report);
    }

    private static function isIncreasing(Collection $report): bool
    {
        return match (true) {
            $report->count() === 1 => true,
            $report->get(0) >= $report->get(1) => false,
            default => static::isIncreasing($report->skip(1)->values()),
        };
    }

    private static function isDecreasing(Collection $report): bool
    {
        return match (true) {
            $report->count() === 1 => true,
            $report->get(0) <= $report->get(1) => false,
            default => static::isDecreasing($report->skip(1)->values()),
        };
    }

    private static function hasValidSteps(Collection $report): bool
    {
        return match (true) {
            $report->count() === 1 => true,
            abs($report->get(0) - $report->get(1)) < 1 => false,
            abs($report->get(0) - $report->get(1)) > 3 => false,
            default => static::hasValidSteps($report->skip(1)->values()),
        };
    }

    private static function createCombinations(Collection $report): LazyCollection
    {
        return LazyCollection::make(function () use ($report) {
            for ($index = 0; $index <= $report->count(); $index++) {
                yield $report->take($index)->concat($report->skip($index + 1));
            }
        });
    }

    private static function parseReports(array $input)
    {
        return collect($input)->map(fn (string $row) => collect(explode(' ', $row)));
    }
}
