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

/**
 * Common interface for retry strategies.
 */
interface StrategyInterface
{
    // The available increment units
    /** @var string */
    const TIME_UNIT_SECONDS = 'seconds';

    /** @var string */
    const TIME_UNIT_MINUTES = 'minutes';

    /** @var string */
    const TIME_UNIT_HOURS   = 'hours';

    /** @var string */
    const TIME_UNIT_DAYS    = 'days';

    /** @var string */
    const TIME_UNIT_MONTHS  = 'months';

    /** @var string */
    const TIME_UNIT_YEARS   = 'years';

    /** @var string[] */
    const TIME_UNITS        = [
        self::TIME_UNIT_SECONDS,
        self::TIME_UNIT_MINUTES,
        self::TIME_UNIT_HOURS,
        self::TIME_UNIT_DAYS,
        self::TIME_UNIT_MONTHS,
        self::TIME_UNIT_YEARS,
    ];

    /**
     * @return bool
     */
    public function canRetry(): bool;

    /**
     * @return int
     */
    public function getAttempts(): int;

    /**
     * @return int
     */
    public function getIncrementBy(): int;

    /**
     * @return int
     */
    public function getMaxAttempts(): int;

    /**
     * @return string
     */
    public function getTimeUnit(): string;

    /**
     * @return string
     */
    public function getStrategyName(): string;

    /**
     * The time to wait in seconds.
     *
     * @return int
     */
    public function waitFor(): int;

    /**
     * @return StrategyInterface
     */
    public function newAttempt(): StrategyInterface;

    /**
     * Returns the DateTime of the next retry or false if no retries have to be done.
     *
     * @return \DateTime|false
     */
    public function retryOn();
}
