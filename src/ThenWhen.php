<?php

namespace SerendipityHQ\Component\ThenWhen;

/**
 * Manges te retry logic implementing various strategies.
 */
class ThenWhen
{
    /**
     * @return TryAgain
     */
    public static function createRetryStrategy()
    {
        return self::createRetryStrategyBuilder()->initializeRetryStrategy();
    }

    /**
     * @return RetryStrategyBuilder
     */
    public static function createRetryStrategyBuilder()
    {
        return new RetryStrategyBuilder();
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
