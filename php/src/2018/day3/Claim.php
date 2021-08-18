<?php

namespace Advent\Year2018;

class Claim
{
    public function __construct(
        public string $ID,
        public int $xStart,
        public int $yStart,
        public int $xLength,
        public int $yLength,
    ) {
    }
}
