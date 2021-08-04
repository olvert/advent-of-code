<?php

namespace Advent;

use Composer\Script\Event;
use Exception;

class Entry
{
    public static function execute(Event $event)
    {
        require_once __DIR__ . '/../vendor/autoload.php';

        [$year, $day, $problem] = $event->getArguments();

        $day = Day::factory($year, $day);

        $solver = match (strtoupper($problem)) {
            'A' => fn() => $day->solveA(),
            'B' => fn() => $day->solveB(),
            default => throw new Exception('Received invalid problem argument: ' . $problem)
        };

        $start = hrtime(true);
        $result = $solver();
        $end = hrtime(true);

        echo 'Result: ' . $result . "\n";
        echo 'Elapsed milliseconds: ' . ($end - $start) / 1000000 . "\n";
    }
}
