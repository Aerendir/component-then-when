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
use SerendipityHQ\Component\ThenWhen\Strategy\LinearStrategy;
use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;

final class LinearStrategyTest extends TestCase
{
    public function testStrategy(): void
    {
        $maxAttempts = 3;
        $incrementBy = 10;
        $timeUnit    = StrategyInterface::TIME_UNIT_SECONDS;
        $resource    = new LinearStrategy($maxAttempts, $incrementBy, $timeUnit);

        self::assertSame($maxAttempts, $resource->getMaxAttempts());
        self::assertSame($incrementBy, $resource->getIncrementBy());
        self::assertSame($timeUnit, $resource->getTimeUnit());
        self::assertSame('linear', $resource->getStrategyName());
        self::assertSame(0, $resource->getAttempts());

        // Test waitFor
        // Attempt 0: 10 * 0 = 0
        self::assertSame(0, $resource->waitFor());

        // Test canRetry and newAttempt
        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(1, $resource->getAttempts());
        // Attempt 1: 10 * 1 = 10
        self::assertSame(10, $resource->waitFor());

        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(2, $resource->getAttempts());
        // Attempt 2: 10 * 2 = 20
        self::assertSame(20, $resource->waitFor());

        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(3, $resource->getAttempts());
        // Attempt 3: 10 * 3 = 30
        self::assertSame(30, $resource->waitFor());

        self::assertFalse($resource->canRetry());

        // Test retryOn
        $resource = new LinearStrategy($maxAttempts, $incrementBy, $timeUnit);

        // Initial state: attempts = 0, waitFor = 0
        $retryOn = $resource->retryOn();
        self::assertInstanceOf(\DateTime::class, $retryOn);
        $expectedTime = (new \DateTime())->modify('+0 seconds');
        self::assertEqualsWithDelta($expectedTime->getTimestamp(), $retryOn->getTimestamp(), 1);

        // Exhaust retries
        $resource->newAttempt()->newAttempt()->newAttempt();
        self::assertFalse($resource->canRetry());
        self::assertFalse($resource->retryOn());
    }

    public function testWaitForWithDifferentUnits(): void
    {
        // 1 attempt, increment 1
        $resource = new LinearStrategy(1, 1, StrategyInterface::TIME_UNIT_SECONDS);
        $resource->newAttempt();
        self::assertSame(1, $resource->waitFor());

        $resource = new LinearStrategy(1, 1, StrategyInterface::TIME_UNIT_MINUTES);
        $resource->newAttempt();
        self::assertSame(60, $resource->waitFor());

        $resource = new LinearStrategy(1, 1, StrategyInterface::TIME_UNIT_HOURS);
        $resource->newAttempt();
        self::assertSame(3600, $resource->waitFor());

        $resource = new LinearStrategy(1, 1, StrategyInterface::TIME_UNIT_DAYS);
        $resource->newAttempt();
        self::assertSame(86400, $resource->waitFor());

        $resource = new LinearStrategy(1, 1, StrategyInterface::TIME_UNIT_MONTHS);
        $resource->newAttempt();
        self::assertSame(2592000, $resource->waitFor()); // 30 days * 86400

        $resource = new LinearStrategy(1, 1, StrategyInterface::TIME_UNIT_YEARS);
        $resource->newAttempt();
        self::assertSame(31104000, $resource->waitFor()); // 12 months * 30 days * 86400
    }

    public function testJsonSerialization(): void
    {
        $maxAttempts = 3;
        $incrementBy = 10;
        $timeUnit    = StrategyInterface::TIME_UNIT_SECONDS;
        $resource    = new LinearStrategy($maxAttempts, $incrementBy, $timeUnit);

        $expected = [
            'attempts'       => 0,
            'max_attempts'   => $maxAttempts,
            'increment_by'   => $incrementBy,
            'increment_unit' => $timeUnit,
        ];

        self::assertSame($expected, $resource->jsonSerialize());
    }
}
