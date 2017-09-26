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

namespace SerendipityHQ\Component\ThenWhen;

/**
 * Manges te retry logic implementing various strategies.
 */
class ThenWhen
{
    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }

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
}
