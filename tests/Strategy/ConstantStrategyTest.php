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
use SerendipityHQ\Component\ThenWhen\Strategy\ConstantStrategy;
use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;

final class ConstantStrategyTest extends TestCase
{
    public function testStrategy(): void
    {
        $maxAttempts = 3;
        $incrementBy = 10;
        $timeUnit = StrategyInterface::TIME_UNIT_SECONDS;
        $resource = new ConstantStrategy($maxAttempts, $incrementBy, $timeUnit);

        self::assertSame($maxAttempts, $resource->getMaxAttempts());
        self::assertSame($incrementBy, $resource->getIncrementBy());
        self::assertSame($timeUnit, $resource->getTimeUnit());
        self::assertSame('constant', $resource->getStrategyName());
        self::assertSame(0, $resource->getAttempts());

        // Test waitFor
        self::assertSame($incrementBy, $resource->waitFor());

        // Test canRetry and newAttempt
        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(1, $resource->getAttempts());
        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(2, $resource->getAttempts());
        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(3, $resource->getAttempts());
        self::assertFalse($resource->canRetry());

        // Test retryOn
        $resource = new ConstantStrategy($maxAttempts, $incrementBy, $timeUnit);
        $retryOn = $resource->retryOn();
        self::assertInstanceOf(\DateTime::class, $retryOn);

        // Verification of the time (approximate since we use 'new \DateTime()')
        $expectedTime = (new \DateTime())->modify('+' . $incrementBy . ' ' . $timeUnit);
        self::assertEqualsWithDelta($expectedTime->getTimestamp(), $retryOn->getTimestamp(), 1);

        // Exhaust retries
        $resource->newAttempt()->newAttempt()->newAttempt();
        self::assertFalse($resource->canRetry());
        self::assertFalse($resource->retryOn());
    }

    public function testWaitForWithDifferentUnits(): void
    {
        $resource = new ConstantStrategy(1, 1, StrategyInterface::TIME_UNIT_SECONDS);
        self::assertSame(1, $resource->waitFor());

        $resource = new ConstantStrategy(1, 1, StrategyInterface::TIME_UNIT_MINUTES);
        self::assertSame(60, $resource->waitFor());

        $resource = new ConstantStrategy(1, 1, StrategyInterface::TIME_UNIT_HOURS);
        self::assertSame(3600, $resource->waitFor());

        $resource = new ConstantStrategy(1, 1, StrategyInterface::TIME_UNIT_DAYS);
        self::assertSame(86400, $resource->waitFor());

        $resource = new ConstantStrategy(1, 1, StrategyInterface::TIME_UNIT_MONTHS);
        self::assertSame(2592000, $resource->waitFor()); // 30 days * 86400

        $resource = new ConstantStrategy(1, 1, StrategyInterface::TIME_UNIT_YEARS);
        self::assertSame(31104000, $resource->waitFor()); // 12 months * 30 days * 86400
    }

    public function testJsonSerialization(): void
    {
        $maxAttempts = 3;
        $incrementBy = 10;
        $timeUnit = StrategyInterface::TIME_UNIT_SECONDS;
        $resource = new ConstantStrategy($maxAttempts, $incrementBy, $timeUnit);

        $expected = [
            'attempts'       => 0,
            'max_attempts'   => $maxAttempts,
            'increment_by'   => $incrementBy,
            'increment_unit' => $timeUnit,
        ];

        self::assertSame($expected, $resource->jsonSerialize());
    }
}
