<?php

require_once __DIR__ . '/Stack.php';

// Config
if (!array_key_exists(1, $argv)) {
    print 'No file specified.' . PHP_EOL;
    exit;
}

if (!file_exists($argv[1])) {
    print 'File does not exist.' . PHP_EOL;
    exit;
}

$brainfuckFile = $argv[1];

// Initialize
$program = str_split(bin2hex(file_get_contents($brainfuckFile)), 2);
$programLength = sizeof($program);
$programPointer = 0;
$memory = array_fill(0, 2048, 0);
$pointer = 0;
$loopPointers = new Stack();
$loopCache = [];

// Execute
while($programPointer < $programLength) {
    switch($program[$programPointer]) {
        case '3e': // >
            $pointer++;
            break;

        case '3c': // <
            $pointer--;
            break;

        case '2b': // +
            $memory[$pointer]++;

            if ($memory[$pointer] > 255) {
                $memory[$pointer] = 0;
            }
            break;

        case '2d': // -
            $memory[$pointer]--;

            if ($memory[$pointer] < 0) {
                $memory[$pointer] = 255;
            }
            break;

        case '2e': // .
            echo chr($memory[$pointer]);
            break;

        case '2c': // ,
            // TODO: Input
            break;

        case '5b': // [
            if ($memory[$pointer] != 0) {
                $loopPointers->Push($programPointer);
            } else if (array_key_exists($programPointer, $loopCache)) {
                $programPointer = $loopCache[$programPointer];
            } else {
                $programPointer++;

                // Skip the loop.
                $currentPointer = $programPointer;
                $depth = 1;

                for ($p = $programPointer; $p < $programLength; $p++) {
                    switch ($program[$p]) {
                        case '5b':
                            $depth++;
                            break;

                        case '5d':
                            $depth--;
                            break;
                    }

                    if ($depth == 0) {
                        $loopCache[$currentPointer] = $p;
                        $programPointer = $p;
                        break;
                    }
                }
            }
            break;

        case '5d': // ]
            $oldPointer = $programPointer;

            $popValue = $loopPointers->Pop();
            if ($popValue !== false) {
                $programPointer = $popValue;
                $loopCache[$programPointer] = $oldPointer;
                $programPointer--;
            }
            break;
    }

    $programPointer++;
}