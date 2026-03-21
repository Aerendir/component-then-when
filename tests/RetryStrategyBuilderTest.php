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
use SerendipityHQ\Component\ThenWhen\RetryStrategyBuilder;
use SerendipityHQ\Component\ThenWhen\Strategy\ConstantStrategy;
use SerendipityHQ\Component\ThenWhen\TryAgain;

final class RetryStrategyBuilderTest extends TestCase
{
    public function testSetStrategyForException(): void
    {
        $builder  = new RetryStrategyBuilder();
        $strategy = new ConstantStrategy(3, 10);

        $builder->setStrategyForException(\RuntimeException::class, $strategy);

        $tryAgain = $builder->initializeRetryStrategy();
        self::assertInstanceOf(TryAgain::class, $tryAgain);

        // Use reflection to check private properties of TryAgain
        $reflection     = new \ReflectionClass($tryAgain);
        $strategiesProp = $reflection->getProperty('strategies');
        $strategies     = $strategiesProp->getValue($tryAgain);

        self::assertArrayHasKey(\RuntimeException::class, $strategies);
        self::assertSame($strategy, $strategies[\RuntimeException::class]);
    }

    public function testSetStrategyForMultipleExceptions(): void
    {
        $builder  = new RetryStrategyBuilder();
        $strategy = new ConstantStrategy(3, 10);

        $builder->setStrategyForException([\RuntimeException::class, \LogicException::class], $strategy);

        $tryAgain = $builder->initializeRetryStrategy();

        $reflection     = new \ReflectionClass($tryAgain);
        $strategiesProp = $reflection->getProperty('strategies');
        $strategies     = $strategiesProp->getValue($tryAgain);

        self::assertArrayHasKey(\RuntimeException::class, $strategies);
        self::assertArrayHasKey(\LogicException::class, $strategies);
        self::assertSame($strategy, $strategies[\RuntimeException::class]);
        self::assertSame($strategy, $strategies[\LogicException::class]);
    }

    public function testSetStrategyForExceptionInterface(): void
    {
        $builder  = new RetryStrategyBuilder();
        $strategy = new ConstantStrategy(3, 10);

        $builder->setStrategyForException(\Throwable::class, $strategy);

        $tryAgain = $builder->initializeRetryStrategy();
        self::assertInstanceOf(TryAgain::class, $tryAgain);

        $reflection     = new \ReflectionClass($tryAgain);
        $strategiesProp = $reflection->getProperty('strategies');
        $strategies     = $strategiesProp->getValue($tryAgain);

        self::assertArrayHasKey(\Throwable::class, $strategies);
        self::assertSame($strategy, $strategies[\Throwable::class]);
    }

    public function testSetStrategyForNonExistentExceptionThrowsException(): void
    {
        $builder  = new RetryStrategyBuilder();
        $strategy = new ConstantStrategy(3, 10);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The exception NonExistentException you want to handle doesn't exist.");

        $builder->setStrategyForException('NonExistentException', $strategy);
    }

    public function testSetMiddleHandlerForException(): void
    {
        $builder  = new RetryStrategyBuilder();
        $strategy = new ConstantStrategy(3, 10);
        $handler  = function (): void {};

        $builder->setStrategyForException(\RuntimeException::class, $strategy);
        $builder->setMiddleHandlerForException(\RuntimeException::class, $handler);

        $tryAgain = $builder->initializeRetryStrategy();

        $reflection   = new \ReflectionClass($tryAgain);
        $handlersProp = $reflection->getProperty('middleHandlers');
        $handlers     = $handlersProp->getValue($tryAgain);

        self::assertArrayHasKey(\RuntimeException::class, $handlers);
        self::assertSame($handler, $handlers[\RuntimeException::class]);
    }

    public function testSetMiddleHandlerWithoutStrategyThrowsException(): void
    {
        $builder = new RetryStrategyBuilder();
        $handler = function (): void {};

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("You are adding a middle handler for the class RuntimeException but you didn't set a Strategy for it.");

        $builder->setMiddleHandlerForException(\RuntimeException::class, $handler);
    }

    public function testSetFinalHandlerForException(): void
    {
        $builder  = new RetryStrategyBuilder();
        $strategy = new ConstantStrategy(3, 10);
        $handler  = function (): void {};

        $builder->setStrategyForException(\RuntimeException::class, $strategy);
        $builder->setFinalHandlerForException(\RuntimeException::class, $handler);

        $tryAgain = $builder->initializeRetryStrategy();

        $reflection   = new \ReflectionClass($tryAgain);
        $handlersProp = $reflection->getProperty('finalHandlers');
        $handlers     = $handlersProp->getValue($tryAgain);

        self::assertArrayHasKey(\RuntimeException::class, $handlers);
        self::assertSame($handler, $handlers[\RuntimeException::class]);
    }

    public function testSetFinalHandlerWithoutStrategyThrowsException(): void
    {
        $builder = new RetryStrategyBuilder();
        $handler = function (): void {};

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("You are adding a final handler for the class RuntimeException but you didn't set a Strategy for it.");

        $builder->setFinalHandlerForException(\RuntimeException::class, $handler);
    }

    public function testPrepareClassesThrowsExceptionForInvalidInput(): void
    {
        $builder  = new RetryStrategyBuilder();
        $strategy = new ConstantStrategy(3, 10);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You have to pass a single Exception class to handle or an array of Exception classes.');

        $builder->setStrategyForException(123, $strategy);
    }
}
