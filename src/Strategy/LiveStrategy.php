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

namespace SerendipityHQ\Component\ThenWhen\Strategy;

/**
 * Waits only one second before retry.
 */
final class LiveStrategy extends ConstantStrategy
{
    /** @var string */
    const STRATEGY = 'live';

    /**
     * @param int $maxAttempts
     */
    public function __construct(int $maxAttempts)
    {
        parent::__construct($maxAttempts, 1, StrategyInterface::TIME_UNIT_SECONDS);
    }
}
