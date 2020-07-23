*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this included!*

# RETRY STRATEGIES

You can choose between multiple retry strategies.

They can also be persisted to the database. To view a real application of them, look at
 [SerendipityHQ Commands Queues Bundle](https://github.com/Aerendir/bundle-commands-queues).

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

# Final Notes

To better understand how to use them, simply see the signature of each strategy or, better, simply try to initialize
 them:

    $strategy = new NeverRetryStrategy(); // Or whichever strategy you would like to use!

*Do you like this bundle? [**Leave a &#9733;**](#js-repo-pjax-container) or run `composer global require symfony/thanks && composer thanks` to say thank you to all libraries you use in your current project, this included!*
