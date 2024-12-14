<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Closure;
use Illuminate\Support\Collection;

class Day8 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $coords = static::parseCoords($input);

        $antinodeGenerator = fn (array $pair) => static::generateAntinodesForA($pair, $coords);

        return static::antinodes($coords, $antinodeGenerator)->count();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $coords = static::parseCoords($input);

        $antinodeGenerator = fn (array $pair) => static::generateAntinodesForB($pair, $coords);

        return static::antinodes($coords, $antinodeGenerator)
            ->concat($coords->reject(fn (string $char) => $char === '.')->keys())
            ->unique()
            ->count();
    }

    private static function antinodes(Collection $coords, Closure $antinodeGenerator): Collection
    {
        return $coords
            ->reject(fn (string $char) => $char === '.')
            ->mapToGroups(fn (string $char, string $coord) => [$char => $coord])
            ->map(static::generatePairs(...))
            ->collapse()
            ->map($antinodeGenerator)
            ->collapse()
            ->unique();
    }

    private static function generateAntinodesForA(array $pair, Collection $coords)
    {
        [$xA, $yA] = static::froomCoord($pair[0]);
        [$xB, $yB] = static::froomCoord($pair[1]);

        $deltaX = $xA - $xB;
        $deltaY = $yA - $yB;

        $nodeA = static::toCoord($xA + $deltaX, $yA + $deltaY);
        $nodeB = static::toCoord($xB - $deltaX, $yB - $deltaY);

        return array_filter([
            $coords->has($nodeA) ? $nodeA : null,
            $coords->has($nodeB) ? $nodeB : null,
        ]);
    }

    private static function generateAntinodesForB(array $pair, Collection $coords)
    {
        [$xA, $yA] = static::froomCoord($pair[0]);
        [$xB, $yB] = static::froomCoord($pair[1]);

        $deltaX = $xA - $xB;
        $deltaY = $yA - $yB;

        $nodes = [];

        $node = static::toCoord($xA + $deltaX, $yA + $deltaY);

        while ($coords->has($node)) {
            $nodes[] = $node;

            [$x, $y] = static::froomCoord($node);

            $node = static::toCoord($x + $deltaX, $y + $deltaY);
        }

        $node = static::toCoord($xB - $deltaX, $yB - $deltaY);

        while ($coords->has($node)) {
            $nodes[] = $node;

            [$x, $y] = static::froomCoord($node);

            $node = static::toCoord($x - $deltaX, $y - $deltaY);
        }

        return $nodes;
    }

    private static function generatePairs(Collection $coords): Collection
    {
        $pairs = collect();

        for ($i = 0; $i < $coords->count() - 1; $i++) {
            for ($j = $i + 1; $j < $coords->count(); $j++) {
                $pairs[] = [$coords->get($i), $coords->get($j)];
            }
        }

        return $pairs;
    }

    private static function parseCoords(array $input): Collection
    {
        return collect($input)
            ->map(fn (string $row) => str_split($row))
            ->map(fn (array $row, int $y) => collect($row)->mapWithKeys(fn (string $char, int $x) => [static::toCoord($x, $y) => $char]))
            ->collapse();
    }

    private static function toCoord(int $x, int $y): string
    {
        return "{$x}:{$y}";
    }

    private static function froomCoord(string $coord): array
    {
        return explode(':', $coord);
    }
}
