<?php

namespace SerendipityHQ\Component\ThenWhen\Strategy;

/**
 * Waits only one second before retry.
 */
class LiveStrategy extends ConstantStrategy
{
    const STRATEGY = 'live';

    /**
     * @param int $maxAttempts
     */
    public function __construct(int $maxAttempts)
    {
        parent::__construct($maxAttempts, 1, StrategyInterface::TIME_UNIT_SECONDS);
    }
}
