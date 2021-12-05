<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;

class Day2 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $instructions = static::parseInput($input);

        $horizontalPosition = 0;
        $depth = 0;

        foreach ($instructions as [$name, $value]) {
            match ($name) {
                'forward' => $horizontalPosition += $value,
                'down' => $depth += $value,
                'up' => $depth -= $value,
            };
        }

        return $horizontalPosition * $depth;
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $instructions = static::parseInput($input);

        $horizontalPosition = 0;
        $depth = 0;
        $aim = 0;

        foreach ($instructions as [$name, $value]) {
            switch ($name) {
                case 'forward':
                    $horizontalPosition += $value;
                    $depth += $aim * $value;
                    break;

                case 'down':
                    $aim += $value;
                    break;
                
                case 'up':
                    $aim -= $value;
                    break;
            };
        }

        return $horizontalPosition * $depth;
    }

    private static function parseInput(array $input): array
    {
        return collect($input)->map(fn ($row) => explode(' ', $row))->all();
    }
}
