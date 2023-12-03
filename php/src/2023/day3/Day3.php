<?php

namespace Advent\Year2023;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day3 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $rowLength = strlen($input[0]);

        $parts = self::parseParts($input);

        $adjacentSymbolIndexes = $parts
            ->reject(fn (array $part) => is_numeric($part['value']))
            ->pluck('indexes')
            ->collapse()
            ->map(fn ($index) => self::adjacentIndexesFromIndex($index, $rowLength))
            ->collapse();

        return $parts
            ->filter(fn (array $part) => is_numeric($part['value']))
            ->filter(fn (array $partNumber) => $partNumber['indexes']->intersect($adjacentSymbolIndexes)->isNotEmpty())
            ->sum('value');

    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $rowLength = strlen($input[0]);

        $parts = self::parseParts($input);

        $partNumbers = $parts->filter(fn (array $part) => is_numeric($part['value']));

        return $parts
            ->filter(fn (array $part) => $part['value'] === '*')
            ->map(fn (array $gear) => $gear['indexes'][0])
            ->map(fn (int $index) => self::adjacentIndexesFromIndex($index, $rowLength))
            ->map(fn (array $adjacentIndexes) => $partNumbers->filter(fn ($partNumber) => $partNumber['indexes']->intersect($adjacentIndexes)->isNotEmpty()))
            ->filter(fn (Collection $numbersAdjacentToGear) => $numbersAdjacentToGear->count() === 2)
            ->map(fn (Collection $numbersAdjacentToGear) => $numbersAdjacentToGear->pluck('value')->reduce(fn (int $product, string $term) => $product * $term, 1))
            ->sum();
    }

    private static function parseParts(array $input): Collection
    {
        return collect($input)
            ->map(fn (string $row) => str_split($row))
            ->collapse()
            ->map(fn (string $value, int $index) => compact('value', 'index'))
            ->chunkWhile(fn (array $point, int $index, Collection $chunk) => is_numeric($point['value']) && is_numeric($chunk->last()['value']))
            ->map(fn (Collection $chunk) => $chunk->where('value', '!==', '.'))
            ->reject(fn (Collection $chunk) => $chunk->isEmpty())
            ->map(fn (Collection $chunk) => [
                'value' => $chunk->pluck('value')->join(''),
                'indexes' => $chunk->pluck('index'),
            ]);
    }

    private static function adjacentIndexesFromIndex(int $index, $rowLength): array
    {
        return [
            $index - $rowLength - 1,
            $index - $rowLength,
            $index - $rowLength + 1,
            $index - 1,
            $index + 1,
            $index + $rowLength - 1,
            $index + $rowLength,
            $index + $rowLength + 1,
        ];
    }
}
