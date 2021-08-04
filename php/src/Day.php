<?php

namespace Advent;

use Exception;

abstract class Day
{
    abstract protected static function solveA();
    abstract protected static function solveB();

    public static function factory(int|string $year, int|string $day): Day
    {
        $class = 'Advent\\Year' . $year . '\\Day' . $day;

        if (!class_exists($class)) {
            throw new Exception('Unable to find class with name: ' . $class);
        }

        return new $class();
    }
}
