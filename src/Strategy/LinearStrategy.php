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
            $incrementBy = $this->getIncrementBy() * $this->getAttempts();

            // ... return the date on which to retry
            return (new \DateTime())->modify('+'.$incrementBy.' '.$this->getTimeUnit());
        }

        return false;
    }
}
