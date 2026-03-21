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
use SerendipityHQ\Component\ThenWhen\Strategy\LiveStrategy;
use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;

final class LiveStrategyTest extends TestCase
{
    public function testStrategy(): void
    {
        $maxAttempts = 5;
        $resource    = new LiveStrategy($maxAttempts);

        self::assertSame($maxAttempts, $resource->getMaxAttempts());
        self::assertSame(1, $resource->getIncrementBy());
        self::assertSame(StrategyInterface::TIME_UNIT_SECONDS, $resource->getTimeUnit());
        self::assertSame('live', $resource->getStrategyName());
        self::assertSame(0, $resource->getAttempts());

        // Test waitFor (should always be 1 second)
        self::assertSame(1, $resource->waitFor());

        // Test canRetry and newAttempt
        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(1, $resource->getAttempts());
        self::assertSame(1, $resource->waitFor());
        self::assertTrue($resource->canRetry());

        // Test retryOn
        $retryOn = $resource->retryOn();
        self::assertInstanceOf(\DateTime::class, $retryOn);

        // Verification of the time (approximate since we use 'new \DateTime()')
        $expectedTime = (new \DateTime())->modify('+1 second');
        self::assertEqualsWithDelta($expectedTime->getTimestamp(), $retryOn->getTimestamp(), 1);

        // Exhaust retries
        $resource->newAttempt()->newAttempt()->newAttempt()->newAttempt();
        self::assertFalse($resource->canRetry());
        self::assertFalse($resource->retryOn());
    }

    public function testJsonSerialization(): void
    {
        $maxAttempts = 5;
        $resource    = new LiveStrategy($maxAttempts);

        $expected = [
            'attempts'       => 0,
            'max_attempts'   => $maxAttempts,
            'increment_by'   => 1,
            'increment_unit' => StrategyInterface::TIME_UNIT_SECONDS,
        ];

        self::assertSame($expected, $resource->jsonSerialize());
    }
}
