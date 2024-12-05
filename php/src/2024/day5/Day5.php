<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;

class Day5 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        [$rules, $updates] = static::parseInput($input);
        
        return $updates
            ->filter(fn (Collection $update) => static::isValidUpdate($rules, $update, $update, collect()))
            ->sum(static::middlePageNumber(...));
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        [$rules, $updates] = static::parseInput($input);
        
        return $updates
            ->reject(fn (Collection $update) => static::isValidUpdate($rules, $update, $update, collect()))
            ->map(fn (Collection $update) => static::reorderToValidUpdate($rules, $update, $update, collect()))
            ->sum(static::middlePageNumber(...));
    }

    private static function isValidUpdate(Collection $rules, Collection $update, Collection $remaining, Collection $visited): bool
    {
        return match (true) {
            $remaining->isEmpty() => true,
            $rules->get($remaining->first())?->intersect($update)->diff($visited)->isNotEmpty() => false,
            default => static::isValidUpdate($rules, $update, $remaining->skip(1), $visited->concat($remaining->take(1))),
        };
    }

    private static function reorderToValidUpdate(Collection $rules, Collection $update, Collection $remaining, Collection $visited): Collection
    {
        if ($remaining->isEmpty()) {
            return $visited;
        }

        if ($rules->get($remaining->first())?->intersect($update)->diff($visited)->isNotEmpty()) {
            return static::reorderToValidUpdate(
                rules: $rules,
                update: $update,
                remaining: $visited->concat($remaining->skip(1))->concat($remaining->take(1)),
                visited: collect(),
            );
        }

        return static::reorderToValidUpdate($rules, $update, $remaining->skip(1), $visited->concat($remaining->take(1)));
    }

    private static function middlePageNumber(Collection $update): int
    {
        return $update->get(intdiv($update->count(), 2));
    }

    private static function parseInput(array $input)
    {
        $rows = collect($input);
        $index = $rows->search('');

        $rules = $rows->take($index - 1)
            ->map(fn (string $rule) => explode('|', $rule))
            ->groupBy(fn (array $rule) => $rule[1])
            ->map(fn (Collection $group) => $group->map(fn (array $rule) => $rule[0]));

        $updates = $rows->skip($index + 1)
            ->map(fn (string $update) => collect(explode(',', $update)));

        return [
            $rules,
            $updates,
        ];
    }
}
