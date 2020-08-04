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

namespace SerendipityHQ\Component\ThenWhen\Tests\Strategy;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Component\ThenWhen\Strategy\NeverRetryStrategy;

final class NeverRetryStrategyTest extends TestCase
{
    public function testStrategy(): void
    {
        $resource = new NeverRetryStrategy();

        self::assertFalse($resource->canRetry());
        self::assertFalse($resource->retryOn());
    }
}
