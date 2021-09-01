<?php

/*
 * This file is part of the Serendipity HQ Then When Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Component\ThenWhen\Strategy;

/**
 * Waits only one second before retry.
 */
final class LiveStrategy extends ConstantStrategy
{
    /** @var string */
    public const STRATEGY = 'live';

    /**
     * @param int $maxAttempts
     */
    public function __construct(int $maxAttempts)
    {
        parent::__construct($maxAttempts, 1, StrategyInterface::TIME_UNIT_SECONDS);
    }
}
