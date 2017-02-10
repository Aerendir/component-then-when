<?php

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
    public function canRetry() : bool
    {
        return $this->attempts < $this->maxAttempts;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttempts() : int
    {
        return $this->attempts;
    }

    /**
     * {@inheritdoc}
     */
    public function getIncrementBy() : int
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
    public function getStrategyName() : string
    {
        return $this::STRATEGY;
    }

    /**
     * {@inheritdoc}
     */
    public function newAttempt() : StrategyInterface
    {
        $this->attempts++;

        return $this;
    }

    /**
     * @param int $increment
     * @param $timeUnit
     *
     * @return int
     */
    protected function convertToSeconds(int $increment, string $timeUnit) : int
    {
        $this->validateTimeUnit($timeUnit);

        switch ($timeUnit) {
            case StrategyInterface::TIME_UNIT_YEARS:
                return $this->convertToSeconds($increment * 12, StrategyInterface::TIME_UNIT_MONTHS);
                break;
            case StrategyInterface::TIME_UNIT_MONTHS:
                // We average to 30 days in a month, without taking care of the ones long 31 days or 28 or 29
                return $this->convertToSeconds($increment * 30, StrategyInterface::TIME_UNIT_DAYS);
                break;
            case StrategyInterface::TIME_UNIT_DAYS:
                return $this->convertToSeconds($increment * 24, StrategyInterface::TIME_UNIT_HOURS);
                break;
            case StrategyInterface::TIME_UNIT_HOURS:
                return $this->convertToSeconds($increment * 60, StrategyInterface::TIME_UNIT_MINUTES);
                break;
            case StrategyInterface::TIME_UNIT_MINUTES:
                return $increment * 60;
                break;
            case StrategyInterface::TIME_UNIT_SECONDS:
                return $increment;
                break;
            default:
                throw new \RuntimeException(sprintf('Unrecognized time unit "%s". Allowed time units are...', $timeUnit));
        }
    }

    /**
     * @param int $attempts
     *
     * @return AbstractStrategy
     */
    protected function setAttempts(int $attempts)  : AbstractStrategy
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * @param int $incremenetBy
     *
     * @return AbstractStrategy
     */
    protected function setIncrementBy(int $incremenetBy)  : AbstractStrategy
    {
        $this->incrementBy = $incremenetBy;

        return $this;
    }

    /**
     * @param string $timeUnit
     *
     * @return AbstractStrategy
     */
    protected function setTimeUnit(string $timeUnit) : AbstractStrategy
    {
        $this->validateTimeUnit($timeUnit);

        $this->timeUnit = $timeUnit;

        return $this;
    }

    /**
     * @param int $maxAttempts
     *
     * @return AbstractStrategy
     */
    protected function setMaxAttempts(int $maxAttempts)  : AbstractStrategy
    {
        $this->maxAttempts = $maxAttempts;

        return $this;
    }

    /**
     * @param string $timeUnit
     *
     * @return bool
     */
    protected function validateTimeUnit(string $timeUnit)
    {
        if (false === in_array($timeUnit, StrategyInterface::TIME_UNITS)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The increment unit "%s" is not supported. Supported increment units are: %s.',
                    $timeUnit, implode(' ', StrategyInterface::TIME_UNITS)
                )
            );
        }

        return $timeUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        if (false === defined(get_class($this).'::STRATEGY')) {
            throw new \RuntimeException(
                'The Strategy doesn\'t tells its own name. Create the contant "STRATEGY" that tells the name of the'
                .' strategy.'
            );
        }

        return [
            'attempts'       => $this->getAttempts(),
            'max_attempts'   => $this->getMaxAttempts(),
            'increment_by'   => $this->getIncrementBy(),
            'increment_unit' => $this->getTimeUnit(),
        ];
    }
}
