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
