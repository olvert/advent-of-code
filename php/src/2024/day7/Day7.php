<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Advent\Year2024\Day7\Operator;
use Illuminate\Support\Collection;

class Day7 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $operators = collect([
            Operator::ADD,
            Operator::MULTIPLY,
        ]);

        return static::solve($input, $operators);
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $operators = collect([
            Operator::ADD,
            Operator::MULTIPLY,
            Operator::CONCATENATION,
        ]);

        return static::solve($input, $operators);
    }

    private static function solve(array $input, Collection $operators): int
    {
        return static::parseInput($input)
            ->filter(fn (Collection $equation) => static::canProduceValue(
                $equation->get('value'),
                $equation->get('numbers')->first(),
                $equation->get('numbers')->skip(1),
                $operators,
            ))
            ->sum('value');
    }

    private static function canProduceValue(int $desiredValue, int $currentValue, Collection $numbers, Collection $operators): bool
    {
        return match (true) {
            $numbers->isEmpty() => $currentValue === $desiredValue,
            $currentValue > $desiredValue => false,
            default => $operators->some(fn (Operator $operator) => static::canProduceValue(
                $desiredValue,
                static::updateValue($currentValue, $operator, $numbers->first()),
                $numbers->skip(1),
                $operators,
            )),
        };
    }

    private static function updateValue(int $value, Operator $operator, int $number): int
    {
        return match ($operator) {
            Operator::ADD => $value + $number,
            Operator::MULTIPLY => $value * $number,
            Operator::CONCATENATION => "{$value}{$number}",
        };
    }

    private static function parseInput(array $input)
    {
        return collect($input)
            ->map(fn (string $row) => explode(': ', $row))
            ->map(fn (array $row) => collect([
                'value' => $row[0],
                'numbers' => collect(explode(' ', $row[1])),
            ]));
    }
}
