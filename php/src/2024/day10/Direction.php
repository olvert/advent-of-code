<?php

namespace Advent\Year2024\Day10;

enum Direction
{
    case UP;
    case RIGHT;
    case DOWN;
    case LEFT;

    public function opposite(): Direction
    {
        return match($this) {
            static::UP => static::DOWN,
            static::RIGHT => static::LEFT,
            static::DOWN => static::UP,
            static::LEFT => static::RIGHT,
        };
    }
}
