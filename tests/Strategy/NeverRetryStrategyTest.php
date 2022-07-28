<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Then When Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
