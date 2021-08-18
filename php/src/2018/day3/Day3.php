<?php

namespace Advent\Year2018;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day3 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');
        $claims = collect($input)
            ->map(fn($row) => static::parseClaim($row))
            ->toArray();

        $fabric = static::mapFabric($claims);

        $overlappedInches = collect($fabric)
            ->flatten(1)
            ->map(fn($inchClaims) => count($inchClaims) > 1 ? 1 : 0)
            ->sum();

        return $overlappedInches;
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');
        $claims = collect($input)
            ->map(fn($row) => static::parseClaim($row));

        $fabric = static::mapFabric($claims);

        $nonOverlappedClaim = $claims
            ->filter(fn($c) => static::claimIsNotOverlapped($fabric, $c))
            ->first();

        return $nonOverlappedClaim->ID;
    }

    private static function mapFabric(array | Collection $claims): array
    {
        $fabric = [];

        foreach ($claims as $c) {
            for ($x = $c->xStart; $x < $c->xStart + $c->xLength; $x++) {
                for ($y = $c->yStart; $y < $c->yStart + $c->yLength; $y++) {
                    $fabric[$x][$y][] = $c->ID;
                }
            }
        }

        return $fabric;
    }

    private static function claimIsNotOverlapped(array $fabric, Claim $c): bool
    {
        for ($x = $c->xStart; $x < $c->xStart + $c->xLength; $x++) {
            for ($y = $c->yStart; $y < $c->yStart + $c->yLength; $y++) {
                if (count($fabric[$x][$y]) > 1) {
                    return false;
                }
            }
        }

        return true;
    }

    private static function parseClaim(string $row): Claim
    {
        $row = str_replace(' ', '', $row);
        $parsed = preg_split('/[@\,:x]/', $row);

        return new Claim(
            $parsed[0],
            intval($parsed[1]),
            intval($parsed[2]),
            intval($parsed[3]),
            intval($parsed[4]),
        );
    }
}
