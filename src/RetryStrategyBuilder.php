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

namespace SerendipityHQ\Component\ThenWhen;

use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;

use function Safe\sprintf;

/**
 * Build a TryAgain object that concretely manages the retries.
 */
final class RetryStrategyBuilder
{
    /** The strategies to use for the various kinds of exception we want to handle */
    private array $strategies = [];

    /** What to do when exceptions are catched during the retryings */
    private array $middleHandlers = [];

    /** What to do when exceptions are catched and no other retries are possible */
    private array $finalHandlers = [];

    /**
     * @param array|string $exceptionClasses
     */
    public function setStrategyForException($exceptionClasses, StrategyInterface $strategy): self
    {
        $exceptionClasses = $this->prepareClasses($exceptionClasses);

        foreach ($exceptionClasses as $exceptionClass) {
            if (false === \class_exists($exceptionClass)) {
                throw new \InvalidArgumentException(sprintf("The exception %s you want to handle doesn't exist.", $exceptionClass));
            }

            $this->strategies[$exceptionClass] = $strategy;
        }

        return $this;
    }

    /**
     * @param array|string $exceptionClasses
     */
    public function setMiddleHandlerForException($exceptionClasses, callable $middleHandler): self
    {
        $exceptionClasses = $this->prepareClasses($exceptionClasses);

        foreach ($exceptionClasses as $exceptionClass) {
            if (false === isset($this->strategies[$exceptionClass])) {
                throw new \InvalidArgumentException(sprintf("You are adding a middle handler for the class %s but you didn't set a Strategy for it." . ' First set a strategy and then set the middle handler.', $exceptionClass));
            }

            // Add the handler if passed
            $this->middleHandlers[$exceptionClass] = $middleHandler;
        }

        return $this;
    }

    /**
     * @param array|string $exceptionClasses
     */
    public function setFinalHandlerForException($exceptionClasses, callable $finalHandler): self
    {
        $exceptionClasses = $this->prepareClasses($exceptionClasses);

        foreach ($exceptionClasses as $exceptionClass) {
            if (false === isset($this->strategies[$exceptionClass])) {
                throw new \InvalidArgumentException(sprintf("You are adding a final handler for the class %s but you didn't set a Strategy for it." . ' First set a strategy and then set the final handler.', $exceptionClass));
            }

            // Add the handler if passed
            $this->finalHandlers[$exceptionClass] = $finalHandler;
        }

        return $this;
    }

    /**
     * Retruns an instace of the TryAgain object.
     */
    public function initializeRetryStrategy(): TryAgain
    {
        return new TryAgain($this->strategies, $this->middleHandlers, $this->finalHandlers);
    }

    /**
     * @param array|string $exceptionClasses
     *
     * @return array|string
     */
    private function prepareClasses($exceptionClasses): array
    {
        if (\is_string($exceptionClasses)) {
            $exceptionClasses = [$exceptionClasses];
        }

        if (false === \is_array($exceptionClasses)) {
            throw new \InvalidArgumentException('You have to pass a single Exception class to handle or an array of Exception classes.');
        }

        return $exceptionClasses;
    }
}
