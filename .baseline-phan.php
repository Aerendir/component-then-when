<?php
/**
 * This is an automatically generated baseline for Phan issues.
 * When Phan is invoked with --load-baseline=path/to/baseline.php,
 * The pre-existing issues listed in this file won't be emitted.
 *
 * This file can be updated by invoking Phan with --save-baseline=path/to/baseline.php
 * (can be combined with --load-baseline)
 */
return [
    // # Issue statistics:
    // PhanUnreferencedPublicMethod : 7 occurrences
    // PhanPluginUnreachableCode : 6 occurrences
    // PhanUnreferencedPublicClassConstant : 4 occurrences
    // PhanUnreferencedClass : 2 occurrences
    // PhanPossiblyInfiniteRecursionSameParams : 1 occurrence
    // PhanTypeMismatchReturn : 1 occurrence
    // PhanUndeclaredConstantOfClass : 1 occurrence
    // PhanUnreferencedProtectedMethod : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/RetryStrategyBuilder.php' => ['PhanUnreferencedPublicMethod'],
        'src/Strategy/AbstractStrategy.php' => ['PhanPluginUnreachableCode', 'PhanTypeMismatchReturn', 'PhanUndeclaredConstantOfClass', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
        'src/Strategy/ConstantStrategy.php' => ['PhanUnreferencedPublicClassConstant'],
        'src/Strategy/ExponentialStrategy.php' => ['PhanUnreferencedPublicClassConstant'],
        'src/Strategy/LinearStrategy.php' => ['PhanUnreferencedPublicClassConstant'],
        'src/Strategy/NeverRetryStrategy.php' => ['PhanUnreferencedPublicClassConstant'],
        'src/ThenWhen.php' => ['PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'src/TryAgain.php' => ['PhanPossiblyInfiniteRecursionSameParams', 'PhanUnreferencedPublicMethod'],
        'tests/Strategy/NeverRetryStrategyTest.php' => ['PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
