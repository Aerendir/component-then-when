<?php

namespace SerendipityHQ\Component\ThenWhen\Strategy;

/**
 * Common interface for retry strategies.
 */
interface StrategyInterface
{
    // The available increment units
    const TIME_UNIT_SECONDS = 'seconds';
    const TIME_UNIT_MINUTES = 'minutes';
    const TIME_UNIT_HOURS = 'hours';
    const TIME_UNIT_DAYS = 'days';
    const TIME_UNIT_MONTHS = 'months';
    const TIME_UNIT_YEARS = 'years';
    const TIME_UNITS = [
        self::TIME_UNIT_SECONDS,
        self::TIME_UNIT_MINUTES,
        self::TIME_UNIT_HOURS,
        self::TIME_UNIT_DAYS,
        self::TIME_UNIT_MONTHS,
        self::TIME_UNIT_YEARS
    ];

    /**
     * @return bool
     */
    public function canRetry(): bool;

    /**
     * @return int
     */
    public function getAttempts() : int;

    /**
     * @return mixed
     */
    public function getIncrementBy() : int;

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
    public function getStrategyName() : string;

    /**
     * Returns the DateTime of the next retry or false if no retries have to be done.
     * @return \DateTime|false
     */
    public function retryOn();
}
