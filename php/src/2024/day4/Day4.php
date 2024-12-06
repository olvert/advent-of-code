<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Advent\Year2024\Day4\Direction;
use Illuminate\Support\Collection;

class Day4 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $coords = static::parseCoords($input);

        $word = collect(['X', 'M', 'A', 'S']);
        $directions = collect(Direction::cases());

        return $coords->keys()
            ->map(fn (string $coord) => static::wordsStartingFromCoord($coords, $coord, $word, $directions))
            ->collapse()
            ->count();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $coords = static::parseCoords($input);

        $word = collect(['M', 'A', 'S']);
        $directions = collect([
            Direction::UP_LEFT,
            Direction::UP_RIGHT,
            Direction::DOWN_RIGHT,
            Direction::DOWN_LEFT,
        ]);

        return $coords->keys()
            ->map(fn (string $coord) => static::wordsStartingFromCoord($coords, $coord, $word, $directions))
            ->collapse()
            ->map(fn (array $word) => static::moveCoord($word['coord'], $word['direction']))
            ->groupBy(fn (string $coord) => $coord)
            ->filter(fn (Collection $group) => $group->count() > 1)
            ->count();
    }

    private static function wordsStartingFromCoord(Collection $coords, string $coord, Collection $word, Collection $validDirections): Collection
    {
        return $validDirections
            ->filter(fn (Direction $direction) => static::search($coords, $direction, $word, $coord))
            ->map(fn (Direction $direction) => [
                'coord' => $coord,
                'direction' => $direction,
            ]);
    }

    private static function search(Collection $coords, Direction $direction, Collection $remainingChars, string $coord): bool
    {
        return match (true) {
            $remainingChars->isEmpty() => true,
            $remainingChars->first() !== $coords->get($coord) => false,
            default => static::search($coords, $direction, $remainingChars->skip(1), static::moveCoord($coord, $direction)),
        };
    }

    private static function toCoord(int $x, int $y): string
    {
        return "{$x}:{$y}";
    }

    private static function moveCoord(string $coord, Direction $direction): string
    {
        [$x, $y] = explode(':', $coord);

        [$deltaX, $deltaY] = match ($direction) {
            Direction::UP_LEFT => [-1, 1],
            Direction::UP => [0, 1],
            Direction::UP_RIGHT => [1, 1],
            Direction::RIGHT => [1, 0],
            Direction::DOWN_RIGHT => [1, -1],
            Direction::DOWN => [0, -1],
            Direction::DOWN_LEFT => [-1, -1],
            Direction::LEFT => [-1, 0],
        };

        return static::toCoord($x + $deltaX, $y + $deltaY);
    }

    private static function parseCoords(array $input): Collection
    {
        return collect($input)
            ->map(fn (string $row) => str_split($row))
            ->map(fn (array $row, int $y) => collect($row)->mapWithKeys(fn (string $char, int $x) => [static::toCoord($x, $y) => $char]))
            ->collapse();
    }
}
