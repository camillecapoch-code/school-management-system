<?php

function calculateAverage(
    float $homework,
    float $test,
    float $exam
): float {

    $average =
        ($homework * 0.20)
        +
        ($test * 0.30)
        +
        ($exam * 0.50);

    return round($average, 2);
}