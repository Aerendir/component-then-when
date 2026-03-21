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

namespace Safe;

if (false === \function_exists('Safe\sprintf')) {
    function sprintf(string $format, ...$args): string
    {
        return \sprintf($format, ...$args);
    }
}

if (false === \function_exists('Safe\sleep')) {
    function sleep(int $seconds): int
    {
        return \sleep($seconds);
    }
}
