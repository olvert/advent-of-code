<?php

namespace Advent\Year2018;

use Advent\Day;
use Advent\InputLoader;

class Day2 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $doubles = 0;
        $triplets = 0;

        foreach ($input as $word) {
            [$hasDouble, $hasTriple] = static::checkWord($word);

            if ($hasDouble) {
                $doubles++;
            }

            if ($hasTriple) {
                $triplets++;
            }
        }

        return $doubles * $triplets;
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');
        $count = count($input);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $diffs = static::getWordDiff($input[$i], $input[$j]);

                if (count($diffs) <= 1) {
                    return static::removeCharAtIndex($input[$i], $diffs[0]);
                }
            }
        }
    }

    private static function checkWord(string $word): array
    {
        $chars = str_split($word);
        $counts = [];

        foreach ($chars as $char) {
            $counts[$char] = isset($counts[$char]) ? $counts[$char] + 1 : 1;
        }

        $collection = collect($counts);

        $hasDouble = $collection->contains(fn($value) => $value === 2);
        $hasTriple = $collection->contains(fn($value) => $value === 3);

        return [
            $hasDouble,
            $hasTriple,
        ];
    }

    private static function getWordDiff(string $word1, string $word2): array
    {
        $chars1 = str_split($word1);
        $chars2 = str_split($word2);
        $count = count($chars1);

        $diffs = [];

        for ($i = 0; $i < $count; $i++) {
            if ($chars1[$i] !== $chars2[$i]) {
                $diffs[] = $i;
            }
        }

        return $diffs;
    }

    private static function removeCharAtIndex(string $string, int $index): string
    {
        $substr1 = substr($string, 0, $index);
        $substr2 = substr($string, $index + 1);

        return $substr1 . $substr2;
    }
}
