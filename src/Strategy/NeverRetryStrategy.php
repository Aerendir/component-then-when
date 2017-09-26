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
