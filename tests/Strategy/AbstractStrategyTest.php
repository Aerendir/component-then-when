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
use SerendipityHQ\Component\ThenWhen\Strategy\AbstractStrategy;
use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;

final class AbstractStrategyTest extends TestCase
{
    public function testJsonSerializeThrowsExceptionWhenStrategyConstantIsMissing(): void
    {
        $resource = new class(1, 1) extends AbstractStrategy {
            public function waitFor(): int
            {
                return 0;
            }

            public function retryOn(): bool
            {
                return false;
            }
        };

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The Strategy doesn\'t tells its own name. Create the contant "STRATEGY" that tells the name of the strategy.');
        $resource->jsonSerialize();
    }

    public function testValidateTimeUnitThrowsExceptionOnInvalidUnit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The increment unit "invalid" is not supported.');

        new class(1, 1, 'invalid') extends AbstractStrategy {
            public const STRATEGY = 'test';

            public function waitFor(): int
            {
                return 0;
            }

            public function retryOn(): bool
            {
                return false;
            }
        };
    }

    public function testConvertToSecondsWithAllUnits(): void
    {
        $resource = new class(1, 1) extends AbstractStrategy {
            public const STRATEGY = 'test';

            public function waitFor(): int
            {
                return 0;
            }

            public function retryOn(): bool
            {
                return false;
            }

            public function publicConvertToSeconds(int $increment, string $timeUnit): int
            {
                return $this->convertToSeconds($increment, $timeUnit);
            }
        };

        self::assertSame(1, $resource->publicConvertToSeconds(1, StrategyInterface::TIME_UNIT_SECONDS));
        self::assertSame(60, $resource->publicConvertToSeconds(1, StrategyInterface::TIME_UNIT_MINUTES));
        self::assertSame(3600, $resource->publicConvertToSeconds(1, StrategyInterface::TIME_UNIT_HOURS));
        self::assertSame(86400, $resource->publicConvertToSeconds(1, StrategyInterface::TIME_UNIT_DAYS));
        self::assertSame(2592000, $resource->publicConvertToSeconds(1, StrategyInterface::TIME_UNIT_MONTHS)); // 30 days
        self::assertSame(31104000, $resource->publicConvertToSeconds(1, StrategyInterface::TIME_UNIT_YEARS)); // 12 * 30 days
    }

    public function testProtectedSetters(): void
    {
        $resource = new class(1, 1) extends AbstractStrategy {
            public const STRATEGY = 'test';

            public function waitFor(): int
            {
                return 0;
            }

            public function retryOn(): bool
            {
                return false;
            }

            public function publicSetAttempts(int $attempts): self
            {
                return $this->setAttempts($attempts);
            }

            public function publicSetIncrementBy(int $incrementBy): self
            {
                return $this->setIncrementBy($incrementBy);
            }

            public function publicSetTimeUnit(string $timeUnit): self
            {
                return $this->setTimeUnit($timeUnit);
            }

            public function publicSetMaxAttempts(int $maxAttempts): self
            {
                return $this->setMaxAttempts($maxAttempts);
            }
        };

        $resource->publicSetAttempts(5);
        self::assertSame(5, $resource->getAttempts());

        $resource->publicSetIncrementBy(10);
        self::assertSame(10, $resource->getIncrementBy());

        $resource->publicSetTimeUnit(StrategyInterface::TIME_UNIT_MINUTES);
        self::assertSame(StrategyInterface::TIME_UNIT_MINUTES, $resource->getTimeUnit());

        $resource->publicSetMaxAttempts(20);
        self::assertSame(20, $resource->getMaxAttempts());
    }

    public function testGetStrategyName(): void
    {
        $resource = new class(1, 1) extends AbstractStrategy {
            public const STRATEGY = 'anonymous_test_strategy';

            public function waitFor(): int
            {
                return 0;
            }

            public function retryOn(): bool
            {
                return false;
            }
        };

        self::assertSame('anonymous_test_strategy', $resource->getStrategyName());
    }

    public function testConvertToSecondsThrowsExceptionOnInvalidUnitDirectly(): void
    {
        $resource = new class(1, 1) extends AbstractStrategy {
            public const STRATEGY = 'test';

            public function waitFor(): int
            {
                return 0;
            }

            public function retryOn(): bool
            {
                return false;
            }

            protected function validateTimeUnit(string $timeUnit): string
            {
                // Override to allow invalid units through
                return $timeUnit;
            }

            public function publicConvertToSeconds(int $increment, string $timeUnit): int
            {
                return $this->convertToSeconds($increment, $timeUnit);
            }
        };

        // Now we can hit the default case in switch
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unrecognized time unit "invalid".');
        $resource->publicConvertToSeconds(1, 'invalid');
    }
}
