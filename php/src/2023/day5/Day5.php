<?php

namespace Advent\Year2023;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Day5 extends Day
{
    public static function solveA()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');

        $chunks = collect($input)
            ->chunkWhile(fn ($row) => ! empty($row))
            ->map(fn (Collection $chunk) => $chunk->filter());

        $rawSeeds = $chunks->shift()->first();

        $mappings = $chunks
            ->map(self::parseMapping(...))
            ->keyBy('sourceKey');

        return Str::of($rawSeeds)
            ->after('seeds: ')
            ->explode(' ')
            ->mapWithKeys(fn (int $seedNumber) => [
                $seedNumber => self::seedToLocation($seedNumber, $mappings),
            ])
            ->min();
    }

    public static function solveB()
    {
        $input = InputLoader::load(__DIR__ . '/input.txt');
    }

    private static function parseMapping(Collection $chunk)
    {
        $rawKeys = $chunk->shift();
        $sourceKey = Str::of($rawKeys)->before('-to-')->toString();
        $destinationKey = Str::of($rawKeys)->after('-to-')->before(' map:')->toString();

        $ranges = self::parseMappingRanges($chunk);

        return compact('sourceKey', 'destinationKey', 'ranges');
    }

    private static function parseMappingRanges(Collection $mappingRanges)
    {
        return $mappingRanges
            ->map(fn (string $rawRange) => explode(' ', $rawRange))
            ->map(fn (array $rangeData) => [
                'sourceStart' => intval($rangeData[1]),
                'sourceEnd' => intval($rangeData[1] + $rangeData[2] - 1),
                'destinationStart' => intval($rangeData[0]),
            ]);
    }

    private static function seedToLocation(int $seedNumber, Collection $mappings): int
    {
        $sourceKey = 'seed';
        $number = $seedNumber;

        do {
            $range = self::findValidRange($number, $mappings[$sourceKey]['ranges']);

            $number = $range
                ? $range['destinationStart'] - $range['sourceStart'] + $number
                : $number;

            $sourceKey = $mappings[$sourceKey]['destinationKey'];
        } while ($sourceKey !== 'location');

        return $number;
    }

    private static function findValidRange(int $number, Collection $ranges): ?array
    {
        return $ranges->firstWhere(fn (array $range) => $number >= $range['sourceStart'] && $number <= $range['sourceEnd']);
    }
}
