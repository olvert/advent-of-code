<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Advent\Year2024\Day10\Direction;
use Closure;
use Illuminate\Support\Collection;

class Day10 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $coords = static::parseCoords($input);

        $trailResolver = fn (string $coord) => static::branchOut($coord, $coords)->unique()->count();

        return static::solve($coords, $trailResolver);
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $coords = static::parseCoords($input);

        $trailResolver = fn (string $coord) => static::branchOut($coord, $coords)->count();

        return static::solve($coords, $trailResolver);
    }

    private static function solve(Collection $coords, Closure $trailResolver): int
    {
        return $coords
            ->filter(fn (int $height) => $height === 0)
            ->keys()
            ->map($trailResolver)
            ->sum();
    }

    private static function searchTrail(string $prevCoord, string $currentCoord, Collection $coords): Collection
    {
        $prevHeight = $coords->get($prevCoord);
        $currentHeight = $coords->get($currentCoord);

        return match (true) {
            $currentHeight === null || $prevHeight === null => collect(),
            $currentHeight - $prevHeight !== 1 => collect(),
            $currentHeight === 9 => collect([$currentCoord]),
            default => static::branchOut($currentCoord, $coords),
        };
    }

    private static function branchOut(string $currentCoord, Collection $coords): Collection
    {
        return collect(Direction::cases())
            ->map(fn (Direction $direction) => static::searchTrail(
                $currentCoord,
                static::moveCoord($currentCoord, $direction),
                $coords
            ))
            ->collapse();
    }

    private static function toCoord(int $x, int $y): string
    {
        return "{$x}:{$y}";
    }

    private static function moveCoord(string $coord, Direction $direction): string
    {
        [$x, $y] = explode(':', $coord);

        [$deltaX, $deltaY] = match ($direction) {
            Direction::UP => [0, -1],
            Direction::RIGHT => [1, 0],
            Direction::DOWN => [0, 1],
            Direction::LEFT => [-1, 0],
        };

        return static::toCoord($x + $deltaX, $y + $deltaY);
    }

    private static function parseCoords(array $input): Collection
    {
        return collect($input)
            ->map(fn (string $row) => str_split($row))
            ->map(fn (array $row, int $y) => collect($row)->mapWithKeys(fn (string $char, int $x) => [static::toCoord($x, $y) => intval($char)]))
            ->collapse();
    }
}
