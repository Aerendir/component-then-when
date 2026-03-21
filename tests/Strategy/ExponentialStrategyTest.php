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
use SerendipityHQ\Component\ThenWhen\Strategy\ExponentialStrategy;
use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;

final class ExponentialStrategyTest extends TestCase
{
    public function testStrategy(): void
    {
        $maxAttempts     = 3;
        $incrementBy     = 10;
        $timeUnit        = StrategyInterface::TIME_UNIT_SECONDS;
        $exponentialBase = 3;
        $resource        = new ExponentialStrategy($maxAttempts, $incrementBy, $timeUnit, $exponentialBase);

        self::assertSame($maxAttempts, $resource->getMaxAttempts());
        self::assertSame($incrementBy, $resource->getIncrementBy());
        self::assertSame($timeUnit, $resource->getTimeUnit());
        self::assertSame('exponential', $resource->getStrategyName());
        self::assertSame(0, $resource->getAttempts());
        self::assertSame($exponentialBase, $resource->getExponentialBase());

        // Test waitFor
        // Attempt 0: 3^0 * 10 = 1 * 10 = 10 (Actually, it returns convertToSeconds(incrementBy, timeUnit) if attempts is 0 or 1 depending on implementation)
        // Looking at the code:
        // $incrementBy = 1 === $this->getAttempts() ? $this->getIncrementBy() : $this->getExponentialBase() ** $this->getAttempts() * $this->getIncrementBy();
        // If attempts is 0: 3^0 * 10 = 1 * 10 = 10.
        self::assertSame(10, $resource->waitFor());

        // Test canRetry and newAttempt
        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(1, $resource->getAttempts());
        // Attempt 1: code says if 1 === attempts, return incrementBy
        self::assertSame(10, $resource->waitFor());

        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(2, $resource->getAttempts());
        // Attempt 2: 3^2 * 10 = 9 * 10 = 90
        self::assertSame(90, $resource->waitFor());

        self::assertTrue($resource->canRetry());
        $resource->newAttempt();
        self::assertSame(3, $resource->getAttempts());
        // Attempt 3: 3^3 * 10 = 27 * 10 = 270
        self::assertSame(270, $resource->waitFor());

        self::assertFalse($resource->canRetry());

        // Test retryOn
        $resource = new ExponentialStrategy($maxAttempts, $incrementBy, $timeUnit, $exponentialBase);
        $retryOn  = $resource->retryOn();
        self::assertInstanceOf(\DateTime::class, $retryOn);

        // Verification of the time (approximate since we use 'new \DateTime()')
        $expectedTime = (new \DateTime())->modify('+' . $resource->waitFor() . ' ' . $timeUnit);
        self::assertEqualsWithDelta($expectedTime->getTimestamp(), $retryOn->getTimestamp(), 1);

        // Exhaust retries
        $resource->newAttempt()->newAttempt()->newAttempt();
        self::assertFalse($resource->canRetry());
        self::assertFalse($resource->retryOn());
    }

    public function testBaseValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The exponential base must be greater than or equal to 2.');
        new ExponentialStrategy(3, 10, StrategyInterface::TIME_UNIT_SECONDS, 1);
    }

    public function testJsonSerialization(): void
    {
        $maxAttempts     = 3;
        $incrementBy     = 10;
        $timeUnit        = StrategyInterface::TIME_UNIT_SECONDS;
        $exponentialBase = 2;
        $resource        = new ExponentialStrategy($maxAttempts, $incrementBy, $timeUnit, $exponentialBase);

        $expected = [
            'attempts'       => 0,
            'max_attempts'   => $maxAttempts,
            'increment_by'   => $incrementBy,
            'increment_unit' => $timeUnit,
        ];

        self::assertSame($expected, $resource->jsonSerialize());
    }
}
