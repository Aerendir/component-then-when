<?php

namespace SerendipityHQ\Component\ThenWhen\Strategy;

/**
 * Ever returns false stopping the retrying.
 */
class NeverRetryStrategy extends AbstractStrategy
{
    const STRATEGY = 'never_retry';

    /**
     * This doesn't accept parameters as ever returns false.
     */
    public function __construct()
    {
        parent::__construct(0, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function retryOn()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function waitFor(): int
    {
        return 0;
    }
}
