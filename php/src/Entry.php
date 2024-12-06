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

        echo PHP_EOL;
        echo 'Result: ' . $result . PHP_EOL;
        echo 'Elapsed milliseconds: ' . ($end - $start) / 1000000 . PHP_EOL;

        static::printPeakMemoryUsage();
    }

    private static function printPeakMemoryUsage(): void
    {
        $peakUsageInBytes = memory_get_peak_usage();
        $peakUsageInMegaBytes = $peakUsageInBytes / (1000 * 1000);

        echo 'Peak memory usage: ' . round($peakUsageInMegaBytes) . 'MB' . PHP_EOL;
    }
}
