<?php

namespace Advent;

use Exception;

class InputLoader
{
    public static function load(string $path): array
    {
        if (!file_exists($path)) {
            throw new Exception('Cannot find file at path: ' . $path);
        }

        $file = file_get_contents($path);

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
