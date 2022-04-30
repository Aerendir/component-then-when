<?php

/*
 * This file is part of the Serendipity HQ Then When Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Component\ThenWhen;

use function Safe\sleep;
use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;

/**
 * Manges te retry logic implementing various strategies.
 */
final class TryAgain
{
    /** @var array $strategies The strategies to use to manage exceptions */
    private array $strategies = [];

    /** @var array $middleHandlers What to do when exceptions are catched during the retryings */
    private array $middleHandlers = [];

    /** @var array $finalHandlers What to do when exceptions are catched and no other retries are possible */
    private array $finalHandlers = [];

    public function __construct(array $strategies, array $middleHandlers, array $finalHandlers)
    {
        $this->strategies     = $strategies;
        $this->middleHandlers = $middleHandlers;
        $this->finalHandlers  = $finalHandlers;
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function try(callable $callback)
    {
        try {
            return \call_user_func($callback);
        } catch (\Throwable $throwable) {
            // An exception were catched: of which type?
            $throwableFQN = \get_class($throwable);

            // If no strategies exist for this exception...
            if (false === isset($this->strategies[$throwableFQN])) {
                // ... throw it
                throw $throwable;
            }

            /** @var StrategyInterface $strategy A strategy exists: use it * */
            $strategy = $this->strategies[$throwableFQN];

            // First check if the operation can be retried
            if (false === $strategy->canRetry()) {
                // The maximum number of attempts is reached: check if there is a final handler
                if (isset($this->finalHandlers[$throwableFQN])) {
                    // Return the result of the set final handler
                    return \call_user_func($this->finalHandlers[$throwableFQN], $throwable);
                }

                // No handler set: throw the exception
                throw $throwable;
            }

            // Increment the attempts counter
            $strategy->newAttempt();

            // Now check if there is a middle handler
            if (isset($this->middleHandlers[$throwableFQN])) {
                $result = \call_user_func($this->middleHandlers[$throwableFQN], $throwable);

                // If the result is false...
                if (false === $result) {
                    // ... We throw the exception
                    throw $throwable;
                }

                // If the result a callable
                if (\is_callable($result)) {
                    // ... we use it to retry
                    return self::try($result);
                }

                // For any other result...
            }

            // Wait the defined time
            sleep($strategy->waitFor());

            // Try again with the same original callable
            return self::try($callback);
        }
    }
}
