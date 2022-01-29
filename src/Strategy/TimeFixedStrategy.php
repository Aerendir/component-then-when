<?php

/*
 * This file is part of the Serendipity HQ Then When Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Component\ThenWhen\Strategy;

use Carbon\Carbon;

/**
 * Calculates the interval between attempts given the max number of attempts and the final DateTime or the max amount of
 * time.
 */
final class TimeFixedStrategy extends LinearStrategy
{
    /** @var string */
    public const STRATEGY = 'time_fixed';

    /**
     * @param string|null $timeUnit
     */
    public function __construct(int $maxAttempts, \DateTimeInterface $endOfTimeWindow, string $timeUnit = null)
    {
        $incrementBy = 1;
        // $endOfTime can be only an integer or a \DateTime
        if (false === \is_int($endOfTimeWindow) && false === $endOfTimeWindow instanceof \DateTime) {
            throw new \InvalidArgumentException('$endOfTimeWindow (second argument) can be only an integer or a \DateTime object.');
        }

        // If $endOfTimeWindow is an integer...
        if (\is_int($endOfTimeWindow)) {
            // We need a valid time unit
            $this->validateTimeUnit($timeUnit);

            // Then we convert all to seconds
            $seconds = $this->convertToSeconds($endOfTimeWindow, $timeUnit);

            // Now we can validate the time window
            $this->validateTimeWindow($maxAttempts, $seconds);

            $incrementBy = \ceil($seconds / $endOfTimeWindow);
        }

        // If $endOfTimeWindow is a \DateTime...
        if ($endOfTimeWindow instanceof \DateTime) {
            // We don't need a $fixedTimeUnit...
            if (null !== $timeUnit) {
                // ... so it's better to alert the developer that one were passed
                throw new \LogicException('A fixed time unit is required only if $fixedTime is an integer but it is a \DateTime object.
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
     * @return float The amount of seconds
     */
    private function calculateIncrementBy(int $maxAttempts, \DateTimeInterface $endOfTimeWindow): float
    {
        $seconds = $endOfTimeWindow->diffInSeconds();

        $this->validateTimeWindow($maxAttempts, $seconds);

        return \ceil($seconds / $maxAttempts);
    }

    private function validateTimeWindow(int $maxAttempts, int $seconds): void
    {
        if ($seconds < $maxAttempts) {
            throw new \LogicException('The given number of max attempts exceeds the available time window. Try to reduce the max amount of' . ' attempts or to increase the available time window.');
        }
    }
}
