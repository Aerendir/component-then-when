<?php

/*
 * This file is part of PHP Value Objects.
 *
 * Copyright Adamo Aerendir Crespi 2017.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2017 Aerendir. All rights reserved.
 * @license   MIT
 */

namespace SerendipityHQ\Component\ThenWhen\Strategy;

use Carbon\Carbon;

/**
 * Calculates the interval between attempts given the max number of attempts and the final DateTime or the max amount of
 * time.
 */
class TimeFixedStrategy extends LinearStrategy
{
    const STRATEGY = 'time_fixed';

    /**
     * @param int           $maxAttempts
     * @param \DateTime|int $endOfTimeWindow
     * @param string|null   $timeUnit
     */
    public function __construct(int $maxAttempts, $endOfTimeWindow, string $timeUnit = null)
    {
        $incrementBy = 1;
        // $endOfTime can be only an integer or a \DateTime
        if (false === is_int($endOfTimeWindow) && false === $endOfTimeWindow instanceof \DateTime) {
            throw new \InvalidArgumentException(
                '$endOfTimeWindow (second argument) can be only an integer or a \DateTime object.'
            );
        }

        // If $endOfTimeWindow is an integer...
        if (is_int($endOfTimeWindow)) {
            // We need a valid time unit
            $this->validateTimeUnit($timeUnit);

            // Then we convert all to seconds
            $seconds = parent::convertToSeconds($endOfTimeWindow, $timeUnit);

            // Now we can validate the time window
            $this->validateTimeWindow($maxAttempts, $seconds);

            $incrementBy = ceil($seconds / $endOfTimeWindow);
        }

        // If $endOfTimeWindow is a \DateTime...
        if ($endOfTimeWindow instanceof \DateTime) {
            // We don't need a $fixedTimeUnit...
            if (null !== $timeUnit) {
                // ... so it's better to alert the developer that one were passed
                throw new \LogicException(
                    'A fixed time unit is required only if $fixedTime is an integer but it is a \DateTime object.
                ');
            }

            // And the DateTime passed MUST be in the future!
            $endOfTimeWindow = Carbon::instance($endOfTimeWindow);
            if ($endOfTimeWindow->isPast()) {
                throw new \LogicException('The fixed time passed is in the past and this is illogical!');
            }

            $incrementBy = $this->calculateIncrementBy($maxAttempts, $endOfTimeWindow);
        }

        // We always have our $incrementBy expressed in seconds
        parent::__construct($maxAttempts, $incrementBy, StrategyInterface::TIME_UNIT_SECONDS);
    }

    /**
     * @param int    $maxAttempts
     * @param Carbon $endOfTimeWindow
     *
     * @return int The amount of seconds
     */
    private function calculateIncrementBy(int $maxAttempts, Carbon $endOfTimeWindow): int
    {
        $seconds = $endOfTimeWindow->diffInSeconds();

        $this->validateTimeWindow($maxAttempts, $seconds);

        return ceil($seconds / $maxAttempts);
    }

    /**
     * @param int $maxAttempts
     * @param int $seconds
     */
    private function validateTimeWindow(int $maxAttempts, int $seconds)
    {
        if ($seconds < $maxAttempts) {
            throw new \LogicException(
                'The given number of max attempts exceeds the available time window. Try to reduce the max amount of'
                . ' attempts or to increase the available time window.'
            );
        }
    }
}
