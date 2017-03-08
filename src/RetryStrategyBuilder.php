<?php

namespace SerendipityHQ\Component\ThenWhen;

use SerendipityHQ\Component\ThenWhen\Strategy\StrategyInterface;

/**
 * Build a TryAgain object that concretely manages the retries.
 */
class RetryStrategyBuilder
{
    /** @var array $strategies The strategies to use for the various kinds of exception we want to handle */
    private $strategies = [];

    /** @var array $middleHandlers What to do when exceptions are catched during the retryings */
    private $middleHandlers = [];

    /** @var array $finalHandlers What to do when exceptions are catched and no other retries are possible */
    private $finalHandlers = [];

    /**
     * @param string|array $exceptionClasses
     * @param StrategyInterface $strategy
     * @return RetryStrategyBuilder
     */
    public function setStrategyForException($exceptionClasses, StrategyInterface $strategy) : RetryStrategyBuilder
    {
        $exceptionClasses = $this->prepareClasses($exceptionClasses);

        foreach ($exceptionClasses as $exceptionClass) {
            if (false === class_exists($exceptionClass)) {
                throw new \InvalidArgumentException(sprintf('The exception %s you want to handle doesn\'t exist.', $exceptionClass));
            }

            $this->strategies[$exceptionClass] = $strategy;
        }

        return $this;
    }

    /**
     * @param string|array $exceptionClasses
     * @param callable $middleHandler
     * @return RetryStrategyBuilder
     */
    public function setMiddleHandlerForException($exceptionClasses, callable $middleHandler) : RetryStrategyBuilder
    {
        $exceptionClasses = $this->prepareClasses($exceptionClasses);

        foreach ($exceptionClasses as $exceptionClass) {
            if (false === isset($this->strategies[$exceptionClass])) {
                throw new \InvalidArgumentException(sprintf(
                    'You are adding a middle handler for the class %s but you didn\'t set a Strategy for it.'
                    . ' First set a strategy and then set the middle handler.',
                    $exceptionClass
                ));
            }

            // Add the handler if passed
            $this->middleHandlers[$exceptionClass] = $middleHandler;
        }

        return $this;
    }

    /**
     * @param string|array $exceptionClasses
     * @param callable $finalHandler
     * @return RetryStrategyBuilder
     */
    public function setFinalHandlerForException($exceptionClasses, callable $finalHandler) : RetryStrategyBuilder
    {
        $exceptionClasses = $this->prepareClasses($exceptionClasses);

        foreach ($exceptionClasses as $exceptionClass) {
            if (false === isset($this->strategies[$exceptionClass])) {
                throw new \InvalidArgumentException(sprintf(
                    'You are adding a final handler for the class %s but you didn\'t set a Strategy for it.'
                    . ' First set a strategy and then set the final handler.',
                    $exceptionClass
                ));
            }

            // Add the handler if passed
            $this->finalHandlers[$exceptionClass] = $finalHandler;
        }

        return $this;
    }

    /**
     * Retruns an instace of the TryAgain object.
     *
     * @return TryAgain
     */
    public function initializeRetryStrategy()
    {
        return new TryAgain($this->strategies, $this->middleHandlers, $this->finalHandlers);
    }

    /**
     * @param array|string $exceptionClasses
     * @return array|string
     */
    private function prepareClasses($exceptionClasses)
    {
        if (is_string($exceptionClasses)) {
            $exceptionClasses = [$exceptionClasses];
        }

        if (false === is_array($exceptionClasses)) {
            throw new \InvalidArgumentException('You have to pass a single Exception class to handle or an array of Exception classes.');
        }

        return $exceptionClasses;
    }
}
