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

/**
 * Abstract class to manage strategies.
 */
abstract class AbstractStrategy implements StrategyInterface
{
    /** @var int $attempts How many retry attempts have been done */
    private $attempts = 0;

    /** @var int $incrementBy */
    private $incrementBy;

    /** @var int $maxAttempts */
    private $maxAttempts = 0;

    /** @var string $timeUnit */
    private $timeUnit;

    /**
     * @param int    $maxAttempts
     * @param int    $incrementBy
     * @param string $timeUnit
     */
    public function __construct(
        int $maxAttempts, int $incrementBy, string $timeUnit = StrategyInterface::TIME_UNIT_SECONDS
    ) {
        $this->setMaxAttempts($maxAttempts)->setIncrementBy($incrementBy)->setTimeUnit($timeUnit);
    }

    /**
     * {@inheritdoc}
     */
    public function canRetry(): bool
    {
        return $this->attempts < $this->maxAttempts;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * {@inheritdoc}
     */
    public function getIncrementBy(): int
    {
        return $this->incrementBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeUnit(): string
    {
        return $this->timeUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function getStrategyName(): string
    {
        return $this::STRATEGY;
    }

    /**
     * {@inheritdoc}
     */
    public function newAttempt(): StrategyInterface
    {
        ++$this->attempts;

        return $this;
    }

    public function jsonSerialize(): array
    {
        if (false === \defined(\get_class($this) . '::STRATEGY')) {
            throw new \RuntimeException(
                'The Strategy doesn\'t tells its own name. Create the contant "STRATEGY" that tells the name of the'
                . ' strategy.'
            );
        }

        return [
            'attempts'       => $this->getAttempts(),
            'max_attempts'   => $this->getMaxAttempts(),
            'increment_by'   => $this->getIncrementBy(),
            'increment_unit' => $this->getTimeUnit(),
        ];
    }

    /**
     * @param int $increment
     * @param $timeUnit
     *
     * @return int
     */
    protected function convertToSeconds(int $increment, string $timeUnit): int
    {
        $this->validateTimeUnit($timeUnit);

        switch ($timeUnit) {
            case StrategyInterface::TIME_UNIT_YEARS:
                return $this->convertToSeconds($increment * 12, StrategyInterface::TIME_UNIT_MONTHS);
            case StrategyInterface::TIME_UNIT_MONTHS:
                // We average to 30 days in a month, without taking care of the ones long 31 days or 28 or 29
                return $this->convertToSeconds($increment * 30, StrategyInterface::TIME_UNIT_DAYS);
            case StrategyInterface::TIME_UNIT_DAYS:
                return $this->convertToSeconds($increment * 24, StrategyInterface::TIME_UNIT_HOURS);
            case StrategyInterface::TIME_UNIT_HOURS:
                return $this->convertToSeconds($increment * 60, StrategyInterface::TIME_UNIT_MINUTES);
            case StrategyInterface::TIME_UNIT_MINUTES:
                return $increment * 60;
            case StrategyInterface::TIME_UNIT_SECONDS:
                return $increment;
            default:
                throw new \RuntimeException(\Safe\sprintf('Unrecognized time unit "%s". Allowed time units are...', $timeUnit));
        }
    }

    protected function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }

    protected function setIncrementBy(int $incremenetBy): self
    {
        $this->incrementBy = $incremenetBy;

        return $this;
    }

    protected function setTimeUnit(string $timeUnit): self
    {
        $this->validateTimeUnit($timeUnit);

        $this->timeUnit = $timeUnit;

        return $this;
    }

    protected function setMaxAttempts(int $maxAttempts): self
    {
        $this->maxAttempts = $maxAttempts;

        return $this;
    }

    protected function validateTimeUnit(string $timeUnit): string
    {
        if (false === \in_array($timeUnit, StrategyInterface::TIME_UNITS)) {
            throw new \InvalidArgumentException(
                \Safe\sprintf(
                    'The increment unit "%s" is not supported. Supported increment units are: %s.',
                    $timeUnit, \implode(' ', StrategyInterface::TIME_UNITS)
                )
            );
        }

        return $timeUnit;
    }
}
