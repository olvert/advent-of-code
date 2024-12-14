<?php

namespace Advent\Year2024;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class Day9 extends Day
{
    public static function solveA()
    {
        $input = static::parseInput();

        $disk = $input->reduce(function (Collection $acc, int $number, int $index) {
            $id = $index % 2 ? '.' : strval(intdiv($index, 2));
            $space = Collection::times($number, fn () => $id);

            return $acc->concat($space);
        }, collect());

        $fileIndex = static::lastFileIndex($disk);
        $spaceIndex = static::firstSpaceIndex($disk);

        $progressBar = new ProgressBar(new ConsoleOutput(), $disk->filter(fn ($char) => $char === '.')->count() - 1);
        $progressBar->setFormat('debug');

        while ($fileIndex > $spaceIndex) {
            $disk->put($spaceIndex, $disk->get($fileIndex));
            $disk->put($fileIndex, '.');

            $fileIndex = static::lastFileIndex($disk);
            $spaceIndex = static::firstSpaceIndex($disk);

            $progressBar->advance();
        }

        $progressBar->finish();

        return static::checksum($disk);
    }

    public static function solveB()
    {
        $input = static::parseInput();

        [$partitions, $disk] = $input->reduce(function ($acc, int $number, int $index) {
            $id = $index % 2 ? '.' : strval(intdiv($index, 2));
            $space = Collection::times($number, fn () => $id);

            return [
                $acc[0]->concat([[
                    'startIndex' => $acc[1]->count(),
                    'id' => $id,
                    'length' => $number,
                ]]),
                $acc[1]->concat($space),
            ];
        }, [collect(), collect()]);

        [$filePartitions, $freeSpacePartitions] = $partitions->partition(fn (array $partition) => $partition['id'] !== '.');

        $progressBar = new ProgressBar(new ConsoleOutput(), $filePartitions->count());
        $progressBar->setFormat('debug');

        foreach ($filePartitions->reverse() as $filePartition) {
            $progressBar->advance();

            $freeSpacePartitionIndex = static::findFreeSpaceIndex($filePartition, $freeSpacePartitions);

            if ($freeSpacePartitionIndex === false) {
                continue;
            }

            $freeSpacePartition = $freeSpacePartitions->get($freeSpacePartitionIndex);

            static::updateDisk($freeSpacePartition['startIndex'], $filePartition['length'], $filePartition['id'], $disk);
            static::updateDisk($filePartition['startIndex'], $filePartition['length'], '.', $disk);

            $freeSpacePartitions->put($freeSpacePartitionIndex, [
                'startIndex' => $freeSpacePartition['startIndex'] + $filePartition['length'],
                'id' => '.',
                'length' => $freeSpacePartition['length'] - $filePartition['length'],
            ]);
        }

        $progressBar->finish();

        return static::checksum($disk);
    }

    private static function updateDisk(int $startIndex, int $length, string $value, Collection $disk): void
    {
        for ($i = $startIndex; $i < $startIndex + $length; $i++) {
            $disk->put($i, $value);
        }
    }

    private static function findFreeSpaceIndex(array $filePartition, Collection $freeSpacePartitions): int|bool
    {
        return $freeSpacePartitions->search(function (array $freeSpacePartition) use ($filePartition) {
            if ($freeSpacePartition['length'] < $filePartition['length']) {
                return false;
            }

            if ($freeSpacePartition['startIndex'] > $filePartition['startIndex']) {
                return false;
            }

            return true;
        });
    }

    private static function checksum(Collection $disk): int
    {
        return $disk
            ->filter(fn ($char) => $char !== '.')
            ->reduce(fn (int $acc, int $id, $index) => $acc + $id * $index, 0);
    }

    private static function lastFileIndex(Collection $disk): int
    {
        for ($i = $disk->count() - 1; $i >= 0; $i--) {
            if ($disk->get($i) !== '.') {
                return $i;
            }
        }
    }

    private static function firstSpaceIndex(Collection $disk): int
    {
        for ($i = 0; $i < $disk->count(); $i++) {
            if ($disk->get($i) === '.') {
                return $i;
            }
        }
    }

    private static function parseInput()
    {
        $input = InputLoader::loadString(__DIR__ . '/test_input.txt');

        $characters = str_split($input);

        return collect($characters);
    }
}
