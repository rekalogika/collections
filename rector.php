<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Strict\Rector\Ternary\DisallowedShortTernaryRuleFixerRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/packages',
        __DIR__ . '/tests/bin',
        __DIR__ . '/tests/config',
        __DIR__ . '/tests/public',
        __DIR__ . '/tests/src',
    ])
    ->withPreparedSets(
        // deadCode: true,
        // codeQuality: true,
        // codingStyle: true,
        // typeDeclarations: true,
        // privatization: true,
        // instanceOf: true,
        // strictBooleans: true,
        // symfonyCodeQuality: true,
        // doctrineCodeQuality: true,
    )
    ->withTypeCoverageLevel(10)
    // uncomment to reach your current PHP version
    ->withPhpSets(php82: true)
    ->withRules([
        // AddOverrideAttributeToOverriddenMethodsRector::class,
    ])
    ->withSkip([
        // static analysis tools don't like this
        RemoveUnusedVariableAssignRector::class,

        // cognitive burden to many people
        SimplifyIfElseToTernaryRector::class,

        CombineIfRector::class => [
            // this 'fixes' symfony makerbundle boilerplate code
            __DIR__ . '/tests/src/App/Entity/*',
        ],

        // potential cognitive burden
        FlipTypeControlToUseExclusiveTypeRector::class,

        // results in too long variables
        CatchExceptionNameMatchingTypeRector::class,

        // makes code unreadable
        DisallowedShortTernaryRuleFixerRector::class,
    ]);
