<?php

namespace SerendipityHQ\Component\ThenWhen\Strategy;

/**
 * Each time adds increment by multiplied by the number of attempts.
 */
class LinearStrategy extends AbstractStrategy
{
    const STRATEGY = 'linear';

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

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function waitFor() : int
    {
        $incrementBy = $this->getIncrementBy() * $this->getAttempts();
        return $this->convertToSeconds($incrementBy, $this->getTimeUnit());
    }
}
