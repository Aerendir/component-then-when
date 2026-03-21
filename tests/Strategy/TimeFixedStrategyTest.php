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
use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;
use SerendipityHQ\Component\ThenWhen\Strategy\TimeFixedStrategy;

final class TimeFixedStrategyTest extends TestCase
{
    public function testStrategyWithIntegerWindow(): void
    {
        $maxAttempts     = 3;
        $endOfTimeWindow = 10;
        $timeUnit        = StrategyInterface::TIME_UNIT_MINUTES;

        // $seconds = 600
        // $incrementBy = ceil(600 / 10) = 60
        $resource = new TimeFixedStrategy($maxAttempts, $endOfTimeWindow, $timeUnit);

        self::assertSame($maxAttempts, $resource->getMaxAttempts());
        self::assertSame(60, $resource->getIncrementBy());
        self::assertSame(StrategyInterface::TIME_UNIT_SECONDS, $resource->getTimeUnit());
        self::assertSame('time_fixed', $resource->getStrategyName());

        // Test waitFor (inherits from LinearStrategy)
        // Attempt 1: 60 * 1 = 60
        $resource->newAttempt();
        self::assertSame(60, $resource->waitFor());

        // Attempt 2: 60 * 2 = 120
        $resource->newAttempt();
        self::assertSame(120, $resource->waitFor());

        // Attempt 3: 60 * 3 = 180
        $resource->newAttempt();
        self::assertSame(180, $resource->waitFor());
    }

    public function testStrategyWithDateTimeWindow(): void
    {
        $maxAttempts     = 5;
        $endOfTimeWindow = new \DateTime('+2 hour');

        // $seconds = 7200
        // $incrementBy = ceil(7200 / 5) = 1440
        $resource = new TimeFixedStrategy($maxAttempts, $endOfTimeWindow);

        self::assertSame($maxAttempts, $resource->getMaxAttempts());
        self::assertSame(1440, $resource->getIncrementBy());
        self::assertSame(StrategyInterface::TIME_UNIT_SECONDS, $resource->getTimeUnit());

        // Attempt 1: 1440 * 1 = 1440
        $resource->newAttempt();
        self::assertSame(1440, $resource->waitFor());

        // Attempt 5: 1440 * 5 = 7200
        $resource->newAttempt()->newAttempt()->newAttempt()->newAttempt();
        self::assertSame(7200, $resource->waitFor());
    }

    public function testExceptionIfIntegerWithoutTimeUnit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The increment unit "" is not supported.');
        new TimeFixedStrategy(3, 10, '');
    }

    public function testExceptionIfDateTimeWithTimeUnit(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('A fixed time unit is required only if $fixedTime is an integer but it is a \DateTime object.');
        new TimeFixedStrategy(3, new \DateTime('+1 hour'), StrategyInterface::TIME_UNIT_SECONDS);
    }

    public function testExceptionIfPastDateTime(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The fixed time passed is in the past and this is illogical!');
        new TimeFixedStrategy(3, new \DateTime('-1 hour'));
    }

    public function testExceptionIfTimeWindowTooSmall(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The given number of max attempts exceeds the available time window.');
        // 10 attempts in 5 seconds window
        new TimeFixedStrategy(10, 5, StrategyInterface::TIME_UNIT_SECONDS);
    }

    public function testJsonSerialization(): void
    {
        $maxAttempts     = 3;
        $endOfTimeWindow = 10;
        $timeUnit        = StrategyInterface::TIME_UNIT_SECONDS;

        // $seconds = 10
        // $incrementBy = ceil(10 / 10) = 1
        $resource = new TimeFixedStrategy($maxAttempts, $endOfTimeWindow, $timeUnit);

        $expected = [
            'attempts'       => 0,
            'max_attempts'   => $maxAttempts,
            'increment_by'   => 1,
            'increment_unit' => StrategyInterface::TIME_UNIT_SECONDS,
        ];

        self::assertSame($expected, $resource->jsonSerialize());
    }
}
