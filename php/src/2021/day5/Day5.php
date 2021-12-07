<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day5 extends Day
{
    public static function solveA()
    {
        $coordinates = static::parseInput()
            ->filter(fn ($line) => static::isHorizontal($line) || static::isVertical($line))
            ->map(fn ($line) => static::lineToCoordinates($line))
            ->collapse();

        return static::produceDiagram($coordinates)
            ->filter(fn ($overlaps) => $overlaps >= 2)
            ->count();
    }

    public static function solveB()
    {
        $coordinates = static::parseInput()
            ->map(fn ($line) => static::lineToCoordinates($line))
            ->collapse();

        return static::produceDiagram($coordinates)
            ->filter(fn ($overlaps) => $overlaps >= 2)
            ->count();
    }

    private static function produceDiagram(Collection $coordinates): Collection
    {
        $diagram = collect();

        foreach ($coordinates as $coordinate) {
            if (! $diagram->has($coordinate)) {
                $diagram[$coordinate] = 0;
            }

            $diagram[$coordinate] += 1;
        }

        return $diagram;
    }

    private static function lineToCoordinates(array $line): array
    {
        return match (true) {
            static::isHorizontal($line) => static::straightLineToCoordinates($line),
            static::isVertical($line) => static::straightLineToCoordinates($line),
            default => static::diagonalLineToCoordinates($line),
        };
    }

    private static function diagonalLineToCoordinates(array $line): array
    {
        $steps = abs($line['x1'] - $line['x2']);

        $goesRight = $line['x1'] < $line['x2'];
        $goesDown = $line['y1'] < $line['y2'];

        $x = $line['x1'];
        $y = $line['y1'];

        $coordinates = [];

        for ($i = 0; $i <= $steps; $i++) {
            $coordinates[] = $x . ',' . $y;

            $x += $goesRight ? 1 : -1;
            $y += $goesDown ? 1 : -1;
        }

        return $coordinates;
    }

    private static function straightLineToCoordinates(array $line): array
    {
        $xStart = min($line['x1'], $line['x2']);
        $xEnd = max($line['x1'], $line['x2']);

        $yStart = min($line['y1'], $line['y2']);
        $yEnd = max($line['y1'], $line['y2']);

        $coordinates = [];

        for ($x = $xStart; $x <= $xEnd; $x++) {
            for ($y = $yStart; $y <= $yEnd; $y++) {
                $coordinates[] = $x . ',' . $y;
            }
        }

        return $coordinates;
    }

    private static function isVertical(array $line): bool
    {
        return $line['x1'] === $line['x2'];
    }

    private static function isHorizontal(array $line): bool
    {
        return $line['y1'] === $line['y2'];
    }

    private static function parseInput(): Collection
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)
            ->map(fn ($row) => preg_split('/,| -> /', $row))
            ->map(fn ($digits) => [
                'x1' => $digits[0],
                'y1' => $digits[1],
                'x2' => $digits[2],
                'y2' => $digits[3],
            ]);
    }
}
