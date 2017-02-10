<?php

namespace SerendipityHQ\Component\ThenWhen\Tests\Strategy;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Component\ThenWhen\Strategy\NeverRetryStrategy;


class NeverRetryStrategyTest extends TestCase
{
    public function testStrategy()
    {
        $resource = new NeverRetryStrategy();

        $this::assertFalse($resource->canRetry());
        $this::assertFalse($resource->retryOn());
    }
}
