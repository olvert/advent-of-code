<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day9 extends Day
{
    public static function solveA()
    {
        $locations = static::parseInput();

        $valueExtractor = static::createValueExtractor($locations);

        return static::parseLowPoints($locations, $valueExtractor)
            ->map(fn ($p) => $valueExtractor($p['x'], $p['y']) + 1)
            ->sum();
    }

    public static function solveB()
    {
        $locations = static::parseInput();

        $valueExtractor = static::createValueExtractor($locations);

        $lowPoints = static::parseLowPoints($locations, $valueExtractor);

        return $lowPoints
            ->map(fn ($point) => static::parseBasinFromLowPoint($locations, $valueExtractor, $point))
            ->map(fn ($basin) => $basin->count())
            ->sortDesc()
            ->take(3)
            ->reduce(fn ($carry, $count) => $carry * $count, 1);
    }

    private static function parseBasinFromLowPoint(Collection $locations, callable $valueExtractor, array $lowPoint): Collection
    {
        $members = collect([
            static::toHash($lowPoint) => true,
        ]);

        $candidates = collect(static::getCandidatesFromPoint($lowPoint, $valueExtractor($lowPoint['x'], $lowPoint['y'])));

        while ($candidates->count() > 0) {
            $candidate = $candidates->shift();
            $candidateValue = $valueExtractor($candidate['x'], $candidate['y']);

            if (static::isValidBasinMember($candidateValue, $candidate['neighbourValue'])) {
                $members[static::toHash($candidate)] = true;
                $candidates->push(...static::getCandidatesFromPoint($candidate, $candidateValue));
            }
        }

        return $members;
    }

    private static function isValidBasinMember(int $candidateValue, int $neighbourValue): bool
    {
        return $candidateValue > $neighbourValue && $candidateValue < 9;
    }

    private static function getCandidatesFromPoint(array $point, int $value): array
    {
        $x = $point['x'];
        $y = $point['y'];

        return [
            [
                'x' => $x - 1,
                'y' => $y,
                'neighbourValue' => $value,
            ],
            [
                'x' => $x + 1,
                'y' => $y,
                'neighbourValue' => $value,
            ],
            [
                'x' => $x,
                'y' => $y - 1,
                'neighbourValue' => $value,
            ],
            [
                'x' => $x,
                'y' => $y + 1,
                'neighbourValue' => $value,
            ],
        ];
    }

    private static function parseLowPoints(Collection $locations, callable $valueExtractor): Collection
    {
        $lowPoints = collect();

        for ($x = 0; $x < $locations->first()->count(); $x++) {
            for ($y = 0; $y < $locations->count(); $y++) {
                if (static::isLowPoint($x, $y, $valueExtractor)) {
                    $lowPoints->push([
                        'x' => $x,
                        'y' => $y,
                    ]);
                }
            }
        }

        return $lowPoints;
    }

    private static function toPoint(string $hash): array
    {
        [$x, $y] = explode(':', $hash);

        return compact('x', 'y');
    }

    private static function toHash(array $point): string
    {
        return $point['x'] . ':' . $point['y'];
    }

    private static function isLowPoint(int $x, int $y, callable $valueExtractor): bool
    {
        $current = $valueExtractor($x, $y);

        $up = $valueExtractor($x, $y - 1);
        $down = $valueExtractor($x, $y + 1);
        $left = $valueExtractor($x - 1, $y);
        $right = $valueExtractor($x + 1, $y);

        return collect([$up, $down, $left, $right])->filter(fn ($value) => $value <= $current)->count() === 0;
    }

    private static function createValueExtractor(Collection $locations): callable
    {
        $xMax = $locations->first()->count();
        $yMax = $locations->count();

        $outOfBoundsValue = $locations->collapse()->max() + 1;

        return static function (int $x, int $y) use ($locations, $xMax, $yMax, $outOfBoundsValue): int {
            if ($x < 0 || $x >= $xMax || $y < 0 || $y >= $yMax) {
                return $outOfBoundsValue;
            }

            return $locations[$y][$x];
        };
    }

    private static function parseInput(): Collection
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        return collect($input)->map(fn ($row) => collect(str_split($row))->map(fn ($number) => intval($number)));
    }
}
