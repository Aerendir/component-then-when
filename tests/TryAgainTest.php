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

namespace SerendipityHQ\Component\ThenWhen\Tests;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Component\ThenWhen\Strategy\ConstantStrategy;
use SerendipityHQ\Component\ThenWhen\TryAgain;

final class TryAgainTest extends TestCase
{
    public function testTrySuccess(): void
    {
        $strategies     = [];
        $middleHandlers = [];
        $finalHandlers  = [];
        $tryAgain       = new TryAgain($strategies, $middleHandlers, $finalHandlers);

        $result = $tryAgain->try(function (): string {
            return 'success';
        });

        self::assertSame('success', $result);
    }

    public function testTryThrowsUnmanagedException(): void
    {
        $strategies     = [];
        $middleHandlers = [];
        $finalHandlers  = [];
        $tryAgain       = new TryAgain($strategies, $middleHandlers, $finalHandlers);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('unmanaged');

        $tryAgain->try(function (): never {
            throw new \RuntimeException('unmanaged');
        });
    }

    public function testTryRetriesAndSucceeds(): void
    {
        $strategy       = new ConstantStrategy(2, 0); // 2 max attempts, 0 wait time for testing
        $strategies     = [\RuntimeException::class => $strategy];
        $middleHandlers = [];
        $finalHandlers  = [];
        $tryAgain       = new TryAgain($strategies, $middleHandlers, $finalHandlers);

        $attempts = 0;
        $result   = $tryAgain->try(function () use (&$attempts): string {
            ++$attempts;
            if ($attempts < 2) {
                throw new \RuntimeException('retry me');
            }

            return 'success';
        });

        self::assertSame('success', $result);
        self::assertSame(2, $attempts);
        self::assertSame(1, $strategy->getAttempts());
    }

    public function testTryReachesMaxAttemptsAndThrows(): void
    {
        $strategy       = new ConstantStrategy(1, 0);
        $strategies     = [\RuntimeException::class => $strategy];
        $middleHandlers = [];
        $finalHandlers  = [];
        $tryAgain       = new TryAgain($strategies, $middleHandlers, $finalHandlers);

        $attempts = 0;
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('retry me');

        try {
            $tryAgain->try(function () use (&$attempts): never {
                ++$attempts;

                throw new \RuntimeException('retry me');
            });
        } finally {
            self::assertSame(2, $attempts); // First try + 1 retry
            self::assertSame(1, $strategy->getAttempts());
            self::assertFalse($strategy->canRetry());
        }
    }

    public function testTryReachesMaxAttemptsAndCallsFinalHandler(): void
    {
        $strategy       = new ConstantStrategy(1, 0);
        $strategies     = [\RuntimeException::class => $strategy];
        $middleHandlers = [];
        $finalHandlers  = [
            \RuntimeException::class => function (\Throwable $t): string {
                return 'handled by final';
            },
        ];
        $tryAgain = new TryAgain($strategies, $middleHandlers, $finalHandlers);

        $attempts = 0;
        $result   = $tryAgain->try(function () use (&$attempts): never {
            ++$attempts;

            throw new \RuntimeException('retry me');
        });

        self::assertSame('handled by final', $result);
        self::assertSame(2, $attempts);
    }

    public function testMiddleHandlerReturnsFalseStopsRetrying(): void
    {
        $strategy       = new ConstantStrategy(2, 0);
        $strategies     = [\RuntimeException::class => $strategy];
        $middleHandlers = [
            \RuntimeException::class => function (\Throwable $t): false {
                return false;
            },
        ];
        $finalHandlers = [];
        $tryAgain      = new TryAgain($strategies, $middleHandlers, $finalHandlers);

        $attempts = 0;
        $this->expectException(\RuntimeException::class);

        try {
            $tryAgain->try(function () use (&$attempts): never {
                ++$attempts;

                throw new \RuntimeException('retry me');
            });
        } finally {
            self::assertSame(1, $attempts);
        }
    }

    public function testMiddleHandlerReturnsCallableRetriesWithNewCallable(): void
    {
        $strategy       = new ConstantStrategy(2, 0);
        $strategies     = [\RuntimeException::class => $strategy];
        $middleHandlers = [
            \RuntimeException::class => function (\Throwable $t): \Closure {
                return function (): string {
                    return 'new callable result';
                };
            },
        ];
        $finalHandlers = [];
        $tryAgain      = new TryAgain($strategies, $middleHandlers, $finalHandlers);

        $result = $tryAgain->try(function (): never {
            throw new \RuntimeException('retry me');
        });

        self::assertSame('new callable result', $result);
    }
}
