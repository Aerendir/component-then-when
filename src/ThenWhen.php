<?php

/*
 * This file is part of the Serendipity HQ Then When Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Component\ThenWhen;

/**
 * Manges te retry logic implementing various strategies.
 */
final class ThenWhen
{
    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }

    public static function createRetryStrategy(): \SerendipityHQ\Component\ThenWhen\TryAgain
    {
        return self::createRetryStrategyBuilder()->initializeRetryStrategy();
    }

    public static function createRetryStrategyBuilder(): \SerendipityHQ\Component\ThenWhen\RetryStrategyBuilder
    {
        return new RetryStrategyBuilder();
    }
}
