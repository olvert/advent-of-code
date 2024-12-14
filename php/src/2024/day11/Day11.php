<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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

        $stones = static::parseInput($input);

        $progressBar = new ProgressBar(new ConsoleOutput(), $numOfBlinks);
        $progressBar->setFormat('debug');

        return static::blink($stones, $numOfBlinks, $progressBar)->count();
    }

    private static function blink(Collection $stones, int $times, ?ProgressBar $bar = null): Collection
    {
        if ($times === 0) {
            $bar?->finish();

            return $stones;
        }
    
        $bar?->advance();

        return static::blink(static::blinkOnce($stones), $times - 1, $bar);
    }

    private static function blinkOnce(Collection $stones): Collection
    {
        return $stones->reduce(function (Collection $acc, int $stone) {
            $evolvedStone = match (true) {
                $stone === 0 => 1,
                Str::of($stone)->length() % 2 === 0 => static::splitStone($stone),
                default => $stone * 2024,
            };

            return $acc->concat(Collection::wrap($evolvedStone));
        }, collect());
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
