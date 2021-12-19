<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day9 extends Day
{
    private const DEFAULT_VALUE = 10;

    public static function solveA()
    {
        $locations = static::parseInput();

        return $locations
            ->filter(fn ($value, $pointHash) => static::isLowPoint($pointHash, $locations))
            ->map(fn ($value) => $value + 1)
            ->sum();
    }

    public static function solveB()
    {
        $locations = static::parseInput();

        $lowPoints = $locations->filter(fn ($value, $pointHash) => static::isLowPoint($pointHash, $locations));

        return $lowPoints
            ->map(fn ($value, $pointHash) => static::parseBasinFromLowPoint($locations, $pointHash))
            ->map(fn ($basin) => $basin->count())
            ->sortDesc()
            ->take(3)
            ->reduce(fn ($carry, $count) => $carry * $count, 1);
    }

    private static function parseBasinFromLowPoint(Collection $locations, string $pointHash): Collection
    {
        $members = collect([
            $pointHash => true,
        ]);

        $candidates = collect(static::getCandidatesFromPoint($pointHash, $locations->get($pointHash)));

        while ($candidates->count() > 0) {
            $candidate = $candidates->shift();
            $value = $locations->get($candidate['pointHash'], static::DEFAULT_VALUE);

            if (static::isValidBasinMember($value, $candidate['neighbourValue'])) {
                $members[$candidate['pointHash']] = true;
                $candidates->push(...static::getCandidatesFromPoint($candidate['pointHash'], $value));
            }
        }

        return $members;
    }

    private static function isValidBasinMember(int $candidateValue, int $neighbourValue): bool
    {
        return $candidateValue > $neighbourValue && $candidateValue < 9;
    }

    private static function getCandidatesFromPoint(string $pointHash, int $value): array
    {
        [$x, $y] = static::toPoint($pointHash);

        return [
            [
                'pointHash' => static::toHash($x - 1, $y),
                'neighbourValue' => $value,
            ],
            [
                'pointHash' => static::toHash($x + 1, $y),
                'neighbourValue' => $value,
            ],
            [
                'pointHash' => static::toHash($x, $y - 1),
                'neighbourValue' => $value,
            ],
            [
                'pointHash' => static::toHash($x, $y + 1),
                'neighbourValue' => $value,
            ],
        ];
    }

    private static function toPoint(string $hash): array
    {
        return explode(':', $hash);
    }

    private static function toHash(int $x, int $y): string
    {
        return $x . ':' . $y;
    }

    private static function isLowPoint(string $pointHash, Collection $locations): bool
    {
        [$x, $y] = static::toPoint($pointHash);
        $current = $locations->get($pointHash);

        $up = $locations->get(static::toHash($x, $y - 1), static::DEFAULT_VALUE);
        $down = $locations->get(static::toHash($x, $y + 1), static::DEFAULT_VALUE);
        $left = $locations->get(static::toHash($x - 1, $y), static::DEFAULT_VALUE);
        $right = $locations->get(static::toHash($x + 1, $y), static::DEFAULT_VALUE);

        return collect([$up, $down, $left, $right])->filter(fn ($value) => $value <= $current)->count() === 0;
    }

    private static function parseInput(): Collection
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $matrix = collect($input)->map(fn ($row) => collect(str_split($row))->map(fn ($number) => intval($number)));

        $locations = [];

        for ($x = 0; $x < $matrix->first()->count(); $x++) {
            for ($y = 0; $y < $matrix->count(); $y++) {
                $locations[static::toHash($x, $y)] = $matrix[$y][$x];
            }
        }

        return collect($locations);
    }
}
