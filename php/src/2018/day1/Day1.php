<?php

namespace Advent\Year2018;

use Advent\Day;
use Advent\InputLoader;

class Day1 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::loadIntegers(__DIR__ . '/input.txt');

        $collection = collect($input);
        
        $frequency = $collection->reduce(fn ($carry, $change) => $carry + $change, 0);
        
        return $frequency;
    }

    public static function solveB()
    {
        $input = InputLoader::loadIntegers(__DIR__ . '/input.txt');

        $count = count($input);
        $index = 0;

        $frequencies = [];
        $current = 0;

        while (true) {
            $current = $current + $input[$index];

            if (array_key_exists($current, $frequencies) && $frequencies[$current] === true) {
                return $current;
            }

            $frequencies[$current] = true;
            $index = ($index + 1) % $count;
        }
    }
}
