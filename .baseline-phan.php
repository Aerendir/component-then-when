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
    // PhanDeprecatedFunction : 6 occurrences
    // PhanUnreferencedPublicClassConstant : 4 occurrences
    // PhanTypeMismatchArgumentNullable : 2 occurrences
    // PhanUndeclaredStaticMethod : 2 occurrences
    // PhanUnreferencedClass : 2 occurrences
    // PhanPossiblyInfiniteRecursionSameParams : 1 occurrence
    // PhanRedefinedExtendedClass : 1 occurrence
    // PhanTypeMismatchDeclaredReturn : 1 occurrence
    // PhanUndeclaredConstantOfClass : 1 occurrence
    // PhanUndeclaredMethod : 1 occurrence
    // PhanUnreferencedProtectedMethod : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/RetryStrategyBuilder.php' => ['PhanDeprecatedFunction', 'PhanTypeMismatchDeclaredReturn', 'PhanUnreferencedPublicMethod'],
        'src/Strategy/AbstractStrategy.php' => ['PhanDeprecatedFunction', 'PhanUndeclaredConstantOfClass', 'PhanUnreferencedProtectedMethod', 'PhanUnreferencedPublicMethod'],
        'src/Strategy/ConstantStrategy.php' => ['PhanUnreferencedPublicClassConstant'],
        'src/Strategy/ExponentialStrategy.php' => ['PhanUnreferencedPublicClassConstant'],
        'src/Strategy/LinearStrategy.php' => ['PhanUnreferencedPublicClassConstant'],
        'src/Strategy/NeverRetryStrategy.php' => ['PhanUnreferencedPublicClassConstant'],
        'src/Strategy/TimeFixedStrategy.php' => ['PhanTypeMismatchArgumentNullable', 'PhanUndeclaredMethod'],
        'src/ThenWhen.php' => ['PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'src/TryAgain.php' => ['PhanDeprecatedFunction', 'PhanPossiblyInfiniteRecursionSameParams', 'PhanUnreferencedPublicMethod'],
        'tests/Strategy/NeverRetryStrategyTest.php' => ['PhanRedefinedExtendedClass', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
