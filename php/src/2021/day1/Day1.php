<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;

class Day1 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $increases = 0;

        for ($i = 1; $i < count($input); $i++) {
            if ($input[$i] > $input[$i - 1]) {
                $increases += 1;
            }
        }

        return $increases;
    }

    public static function solveB()
    {
        $input = collect(InputLoader::load(__DIR__ . '/input.txt'));

        $increases = 0;

        for ($i = 4; $i < $input->count() + 2; $i++) {
            if ($input->slice($i - 3, 3)->sum() > $input->slice($i - 4, 3)->sum()) {
                $increases += 1;
            }
        }

        return $increases;
    }
}
