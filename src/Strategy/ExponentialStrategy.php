<?php

namespace SerendipityHQ\Component\ThenWhen\Strategy;

class ExponentialStrategy extends AbstractStrategy
{
    const STRATEGY = 'exponential';

    /** @var int $exponentialBase */
    private $exponentialBase;

    /**
     * @param int    $maxAttempts
     * @param int    $incrementBy
     * @param string $timeUnit
     * @param int    $exponentialBase
     */
    public function __construct(
        int $maxAttempts,
        int $incrementBy,
        string $timeUnit = StrategyInterface::TIME_UNIT_SECONDS,
        int $exponentialBase = 2
    ) {
        $this->setExponentialBase($exponentialBase);

        parent::__construct($maxAttempts, $incrementBy, $timeUnit);
    }

    /**
     * {@inheritdoc}
     */
    public function retryOn()
    {
        // If we can retry...
        if (parent::canRetry()) {
            $incrementBy = $this->getAttempts() === 1
                ? $this->getIncrementBy()
                : pow($this->getExponentialBase(), $this->getAttempts()) * $this->getIncrementBy();

            // ... return the date on which to retry
            return (new \DateTime())->modify('+'.$incrementBy.' '.$this->getTimeUnit());
        }

        // No more retries
        return false;
    }

    /**
     * @return int
     */
    public function getExponentialBase(): int
    {
        return $this->exponentialBase;
    }

    /**
     * @param int $exponentialBase
     *
     * @return AbstractStrategy
     */
    protected function setExponentialBase(int $exponentialBase) : AbstractStrategy
    {
        if (2 > $exponentialBase) {
            throw new \InvalidArgumentException('The exponential base must be greater than or equal to 2.');
        }

        $this->exponentialBase = $exponentialBase;

        return $this;
    }
}
