<?php

namespace Advent;

use Exception;

class InputLoader
{
    public static function loadString(string $path): string
    {
        if (!file_exists($path)) {
            throw new Exception('Cannot find file at path: ' . $path);
        }

        return file_get_contents($path);
    }

    public static function load(string $path): array
    {
        $file = static::loadString($path);

        return explode("\n", $file);
    }

    public static function loadIntegers(string $path): array
    {
        $input = static::load($path);

        return collect($input)
            ->map(fn ($value) => intval($value))
            ->toArray();
    }
}
