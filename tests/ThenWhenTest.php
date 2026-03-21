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
use SerendipityHQ\Component\ThenWhen\ThenWhen;
use SerendipityHQ\Component\ThenWhen\TryAgain;

final class ThenWhenTest extends TestCase
{
    public function testCreateRetryStrategy(): void
    {
        $tryAgain = ThenWhen::createRetryStrategy();
        self::assertInstanceOf(TryAgain::class, $tryAgain);
    }

    public function testCreateRetryStrategyBuilder(): void
    {
        $builder = ThenWhen::createRetryStrategyBuilder();
        self::assertInstanceOf(RetryStrategyBuilder::class, $builder);
    }

    public function testConstructorIsPrivate(): void
    {
        $reflection  = new \ReflectionClass(ThenWhen::class);
        $constructor = $reflection->getConstructor();

        self::assertNotNull($constructor);
        self::assertTrue($constructor->isPrivate());
    }
}
