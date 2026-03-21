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

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

use function Safe\sleep;
use function Safe\sprintf;

/**
 * Tests the functions in src/functions.php.
 */
final class FunctionsTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testSafeSprintf(): void
    {
        // Ensure the function is available
        if (false === \function_exists('Safe\sprintf')) {
            require_once __DIR__ . '/../src/functions.php';
        }

        self::assertTrue(\function_exists('Safe\sprintf'));
        self::assertSame('Hello world', sprintf('Hello %s', 'world'));
    }

    #[RunInSeparateProcess]
    public function testSafeSleep(): void
    {
        // Ensure the function is available
        if (false === \function_exists('Safe\sleep')) {
            require_once __DIR__ . '/../src/functions.php';
        }

        self::assertTrue(\function_exists('Safe\sleep'));

        $start = \microtime(true);
        sleep(1);
        $end = \microtime(true);

        self::assertGreaterThanOrEqual(1.0, $end - $start);
    }
}
