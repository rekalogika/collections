<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\Use_\SeparateMultiUseImportsRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
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
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        strictBooleans: true,
        symfonyCodeQuality: true,
        doctrineCodeQuality: true,
    )
    ->withPhpSets(php82: true)
    ->withRules([
        // AddOverrideAttributeToOverriddenMethodsRector::class,
    ])
    ->withSkip([
        RemoveUselessParamTagRector::class => [
            // static analysis tools don't like this
            __DIR__ . '/packages/collections-contracts/src/ReadableRecollection.php',
            __DIR__ . '/packages/collections-contracts/src/Recollection.php',
        ],
        RemoveUselessReturnTagRector::class => [
            // static analysis tools don't like this
            __DIR__ . '/packages/collections-common/src/Trait/ReadableCollectionTrait.php',
            __DIR__ . '/packages/collections-common/src/Trait/MinimalReadableRecollectionTrait.php',
            __DIR__ . '/packages/collections-common/src/Trait/ReadableRecollectionTrait.php',
            __DIR__ . '/packages/collections-contracts/src/MinimalReadableRecollection.php',
            __DIR__ . '/packages/collections-domain/src/Trait/CriteriaReadableTrait.php',
            __DIR__ . '/packages/collections-domain/src/Trait/ReadableExtraLazyTrait.php',
            __DIR__ . '/packages/collections-orm/src/Trait/MinimalReadableRepositoryTrait.php',
        ],

        RemoveUnusedPublicMethodParameterRector::class => [
            // @todo temporary
            __DIR__ . '/packages/collections-domain/src/Internal/ExtraLazyDetector.php',
        ],

        // static analysis tools don't like this
        RemoveNonExistingVarAnnotationRector::class,

        RemoveExtraParametersRector::class => [
            // @todo temporary
            __DIR__ . '/packages/collections-domain/src/Internal/ExtraLazyDetector.php',
        ],

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

        // unsafe
        SeparateMultiUseImportsRector::class,
    ]);
