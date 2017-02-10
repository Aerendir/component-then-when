# RETRY STRATEGIES

You can choose between multiple retry strategies.

Here they are described in details.

## NeverRetryStrategy

When this strategy is set, no retry attempts have to be done.

`getNextAttemptOn()` will return immediately `false`.

## Live Strategy

Retry ASAP with the minimum time of wait (close to 0).

## Constant Strategy

On each retry ever adds the same amount of time.

## Exponential Strategy

Adds an exponential time using the `pow()` PHP's function.

## Linear Strategy

Multiply the base amount of time by the number of attempts.

## TimeFixed Strategy

Divides the number of max attempts by the max time you want to wait. 
