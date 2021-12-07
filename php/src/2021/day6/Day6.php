<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;
use Generator;
use Illuminate\Support\Collection;

class Day6 extends Day
{
    public static function solveA()
    {
        $initialFishes = static::parseInput();
        $totalDays = 80;

        return static::progressDaysRecursive($initialFishes, $totalDays)->count();
    }

    public static function solveB()
    {
        $initialFishes = static::parseInput();
        $totalDays = 256;

        return static::progressDaysAsTimerSums($initialFishes, $totalDays);
    }

    private static function progressDaysAsTimerSums(Collection $initialFishes, int $days): int
    {
        $timerSums = array_fill(0, 9, 0);

        foreach ($initialFishes as $timer) {
            $timerSums[$timer] += 1;
        }

        while ($days > 0) {
            $replicatingSum = array_shift($timerSums);
            $timerSums[6] += $replicatingSum;
            $timerSums[] = $replicatingSum;

            $days -= 1;
        }

        return collect($timerSums)->sum();
    }

    /**
     * Peak memory usage with `input.txt` and 80 days: 10MB
     * (basically capped at 11MB regardless of input size but very slow)
     */
    private static function progressDaysGenerator(Collection $initialFishes, int $totalDays): int
    {
        $numOfFishes = 0;

        foreach ($initialFishes as $fish) {
            $fishGenerator = static::progressFishGenerator($fish, $totalDays);

            while ($fishGenerator->valid()) {
                $numOfFishes += 1;
                $fishGenerator->next();

                if ($numOfFishes % 1000000 === 0) {
                    dump('numOfFishes: ' . ($numOfFishes / 1000000) . 'M');
                }
            }
        }

        return $numOfFishes;
    }

    /**
     * Peak memory usage with `input.txt` and 80 days: 10MB
     */
    private static function progressDaysLoop(Collection $initialFishes, int $totalDays): int
    {
        $numOfFishes = 0;

        foreach ($initialFishes as $fish) {
            $numOfFishes += static::progressDays(collect([$fish]), $totalDays)->count();
        }

        return $numOfFishes;
    }

    /**
     * Peak memory usage with `input.txt` and 80 days: 77MB
     */
    private static function progressDays(Collection $fishes, int $days): Collection
    {
        while ($days > 0) {
            $fishes = static::progressFishes($fishes);
            $days -= 1;
        }

        return $fishes;
    }

    /**
     * Peak memory usage with `input.txt` and 80 days: 245MB
     */
    private static function progressDaysRecursive(Collection $fishes, int $days): Collection
    {
        if ($days === 0) {
            return $fishes;
        }

        return static::progressDaysRecursive(
            static::progressFishes($fishes),
            $days - 1
        );
    }

    private static function progressFishGenerator(int $fish, int $days): Generator
    {
        if ($days <= 0) {
            yield $fish;
        } elseif ($fish === 0) {
            yield from static::progressFishGenerator(6, $days - 1);
            yield from static::progressFishGenerator(8, $days - 1);
        } else {
            yield from static::progressFishGenerator($fish - 1, $days - 1);
        }
    }

    private static function progressFishes(Collection $fishes): Collection
    {
        return $fishes->map(fn ($fish) => match ($fish) {
            0 => [6, 8],
            default => $fish - 1,
        })->flatten();
    }

    private static function parseInput(): Collection
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect(explode(',', $input[0]))->map(fn ($fish) => intval($fish));
    }
}
