<?php

namespace Advent\Year2021;

use Advent\Day;
use Advent\InputLoader;
use Illuminate\Support\Collection;
use Spatie\CollectionMacros\Macros\Transpose;

class Day4 extends Day
{
    public static function solveA()
    {
        Collection::macro('transpose', (new Transpose())());

        [$numbers, $boards] = static::parseInput();

        $winningBoard = null;
        $currentNumber = null;

        while (! $winningBoard) {
            $currentNumber = $numbers->shift();
            $boards = $boards->map(fn ($board) => static::markNumber($board, $currentNumber));
            $winningBoard = $boards->filter(fn ($board) => static::hasWinCombination($board))->first();
        }

        return $currentNumber * static::sumUnmarkedNumbers($winningBoard);
    }

    public static function solveB()
    {
        Collection::macro('transpose', (new Transpose())());

        [$numbers, $boards] = static::parseInput();

        $lastWinningGame = $boards
            ->map(fn ($board) => static::simulateGame($board, collect()->merge($numbers)))
            ->sortByDesc(fn ($simulatedGame) => $simulatedGame['numbersNeeded'])
            ->first();

        return $lastWinningGame['winningNumber'] * static::sumUnmarkedNumbers($lastWinningGame['board']);
    }

    private static function simulateGame(Collection $board, Collection $numbers): Collection
    {
        $currentNumber = null;
        $numbersUsed = 0;

        while (! static::hasWinCombination($board)) {
            $currentNumber = $numbers->shift();
            $board = static::markNumber($board, $currentNumber);
            $numbersUsed += 1;
        }

        return collect([
            'board' => $board,
            'winningNumber' => $currentNumber,
            'numbersNeeded' => $numbersUsed,
        ]);
    }

    private static function markNumber(Collection $board, string $number): Collection
    {
        return $board
            ->collapse()
            ->map(fn ($slot) => [
                'number' => $slot['number'],
                'marked' => $slot['number'] === $number ? true : $slot['marked'],
            ])
            ->chunk(5);
    }

    private static function hasWinCombination(Collection $board): bool
    {
        return collect()
            ->merge($board)
            ->merge($board->transpose())
            ->map(fn ($rowOrColumn) => $rowOrColumn->every('marked'))
            ->some(true);
    }

    private static function sumUnmarkedNumbers(Collection $board): int
    {
        return $board
            ->collapse()
            ->filter(fn ($slot) => $slot['marked'] === false)
            ->pluck('number')
            ->sum();
    }

    private static function parseInput(): array
    {
        $input = collect(InputLoader::load(__DIR__ . '/input.txt'));

        $numbers = collect(explode(',', $input->shift()));

        $boards = $input
            ->map(fn ($r) => explode(' ', $r))
            ->collapse()
            ->filter(fn ($d) => $d !== '')
            ->map(fn ($d) => [
                'number' => $d,
                'marked' => false,
            ])
            ->chunk(25)
            ->map(fn ($chunk) => $chunk->chunk(5));
        
        return [$numbers, $boards];
    }
}
