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
        self::assertSame(0, $resource->waitFor());
        self::assertSame('never_retry', $resource->getStrategyName());
        self::assertSame(0, $resource->getAttempts());
        self::assertSame(0, $resource->getIncrementBy());
        self::assertSame(0, $resource->getMaxAttempts());
        self::assertSame('seconds', $resource->getTimeUnit());

        $resource->newAttempt();
        self::assertSame(1, $resource->getAttempts());
        self::assertFalse($resource->canRetry());
    }

    public function testJsonSerialize(): void
    {
        $resource = new NeverRetryStrategy();

        $expected = [
            'attempts'       => 0,
            'max_attempts'   => 0,
            'increment_by'   => 0,
            'increment_unit' => 'seconds',
        ];

        self::assertSame($expected, $resource->jsonSerialize());
    }
}
