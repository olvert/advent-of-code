<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Advent\Year2024\Day6\Direction;
use Advent\Year2024\Day6\Position;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class Day6 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $coords = static::parseCoords($input);
        $startingCoord = $coords->search(Position::START);

        return static::walk($coords, collect([$startingCoord]), $startingCoord, Direction::UP)
            ->unique()
            ->count();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $coords = static::parseCoords($input);
        $startingCoord = $coords->search(Position::START);

        return static::createCandidates($coords)
            ->map(fn (Collection $coordsWithObstacle) => static::hasLoop($coordsWithObstacle, collect([$startingCoord]), $startingCoord, Direction::UP))
            ->filter()
            ->count();
    }

    private static function hasLoop(Collection $coords, Collection $path, string $currentCord, Direction $direction): bool
    {
        $nextCoord = static::moveCoord($currentCord, $direction);
        $pathIndex = $path->search($currentCord);

        if ($pathIndex !== false && $path->get($pathIndex + 1) === $nextCoord) {
            return true;
        }

        return match ($coords->get($nextCoord)) {
            null => false,
            Position::WALKABLE, Position::START => static::hasLoop($coords, $path->concat([$nextCoord]), $nextCoord, $direction),
            Position::OBSTRUCTION => static::hasLoop($coords, $path, $currentCord, static::turn($direction)),
        };
    }

    private static function walk(Collection $coords, Collection $path, string $currentCord, Direction $direction): Collection
    {
        $nextCoord = static::moveCoord($currentCord, $direction);

        return match ($coords->get($nextCoord)) {
            null => $path,
            Position::WALKABLE, Position::START => static::walk($coords, $path->concat([$nextCoord]), $nextCoord, $direction),
            Position::OBSTRUCTION => static::walk($coords, $path, $currentCord, static::turn($direction)),
        };
    }

    private static function turn(Direction $direction): Direction
    {
        return match ($direction) {
            Direction::UP => Direction::RIGHT,
            Direction::RIGHT => Direction::DOWN,
            Direction::DOWN => Direction::LEFT,
            Direction::LEFT => Direction::UP,
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
            Direction::UP => [0, -1],
            Direction::RIGHT => [1, 0],
            Direction::DOWN => [0, 1],
            Direction::LEFT => [-1, 0],
        };

        return static::toCoord($x + $deltaX, $y + $deltaY);
    }

    private static function createCandidates(Collection $coords): LazyCollection
    {
        $coordsToReplaceWithObstacle = $coords
            ->filter(fn (Position $position) => $position === Position::WALKABLE)
            ->keys();

        $progressBar = new ProgressBar(new ConsoleOutput(), $coordsToReplaceWithObstacle->count());
        $progressBar->setFormat('debug');

        return LazyCollection::make(function () use ($coords, $coordsToReplaceWithObstacle, $progressBar) {
            for ($index = 0; $index < $coordsToReplaceWithObstacle->count(); $index++) {
                yield $coords->merge([$coordsToReplaceWithObstacle[$index] => Position::OBSTRUCTION]);

                $progressBar->advance();
            }

            $progressBar->finish();
        });
    }

    private static function parseCoords(array $input): Collection
    {
        return collect($input)
            ->map(fn (string $row) => str_split($row))
            ->map(fn (array $row, int $y) => collect($row)->mapWithKeys(fn (string $char, int $x) => [static::toCoord($x, $y) => static::charToPosition($char)]))
            ->collapse();
    }

    private static function charToPosition(string $char): Position
    {
        return match ($char) {
            '#' => Position::OBSTRUCTION,
            '.' => Position::WALKABLE,
            '^' => Position::START,
        };
    }
}
