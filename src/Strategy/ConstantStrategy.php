<?php

namespace SerendipityHQ\Component\ThenWhen\Strategy;

/**
 * Each time simply adds the increment by value.
 */
class ConstantStrategy extends AbstractStrategy
{
    const STRATEGY = 'constant';

    /**
     * {@inheritdoc}
     */
    public function retryOn()
    {
        // If we can retry...
        if (parent::canRetry()) {
            // ... return the date on which to retry
            return (new \DateTime())->modify('+'.$this->waitFor().' '.self::TIME_UNIT_SECONDS);
        }

        // No more retries
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function waitFor() : int
    {
        return $this->convertToSeconds($this->getIncrementBy(), $this->getTimeUnit());
    }
}
