<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/collections package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\Domain\Collections\Internal;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;

use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use Rekalogika\Contracts\Collections\Exception\UnexpectedValueException;

/**
 * Copied from ClosureExpressionVisitor from doctrine/collections package.
 */
class DirectClosureExpressionVisitor extends ExpressionVisitor
{
    /**
     * Accesses the field of a given object. This field has to be public
     * directly or indirectly (through an accessor get*, is*, or a magic
     * method, __get, __call).
     *
     * @param object|mixed[] $object
     *
     * @return mixed
     */
    public static function getObjectFieldValue(object|array $object, string $field)
    {
        if (str_contains($field, '.')) {
            [$field, $subField] = explode('.', $field, 2);
            $object             = self::getObjectFieldValue($object, $field);

            return self::getObjectFieldValue($object, $subField);
        }

        if (\is_array($object)) {
            return $object[$field];
        }

        $reflection = new \ReflectionObject($object);

        do {
            if ($reflection->hasProperty($field)) {
                $property = $reflection->getProperty($field);
                $property->setAccessible(true);

                return $property->getValue($object);
            }
        } while ($reflection = $reflection->getParentClass());

        throw new UnexpectedValueException('Unknown field ' . $field);
    }

    /**
     * Helper for sorting arrays of objects based on multiple fields + orientations.
     *
     * @return \Closure
     */
    public static function sortByField(string $name, int $orientation = 1, \Closure|null $next = null)
    {
        if (!$next) {
            $next = static fn (): int => 0;
        }

        return static function ($a, $b) use ($name, $next, $orientation): int {
            $aValue = static::getObjectFieldValue($a, $name);

            $bValue = static::getObjectFieldValue($b, $name);

            if ($aValue === $bValue) {
                return $next($a, $b);
            }

            return ($aValue > $bValue ? 1 : -1) * $orientation;
        };
    }

    /**
     * {@inheritDoc}
     */
    public function walkComparison(Comparison $comparison)
    {
        $field = $comparison->getField();
        $value = $comparison->getValue()->getValue();

        return match ($comparison->getOperator()) {
            Comparison::EQ => static fn ($object): bool => static::getObjectFieldValue($object, $field) === $value,
            Comparison::NEQ => static fn ($object): bool => static::getObjectFieldValue($object, $field) !== $value,
            Comparison::LT => static fn ($object): bool => static::getObjectFieldValue($object, $field) < $value,
            Comparison::LTE => static fn ($object): bool => static::getObjectFieldValue($object, $field) <= $value,
            Comparison::GT => static fn ($object): bool => static::getObjectFieldValue($object, $field) > $value,
            Comparison::GTE => static fn ($object): bool => static::getObjectFieldValue($object, $field) >= $value,
            Comparison::IN => static function ($object) use ($field, $value): bool {
                $fieldValue = static::getObjectFieldValue($object, $field);

                return \in_array($fieldValue, $value, \is_scalar($fieldValue));
            },
            Comparison::NIN => static function ($object) use ($field, $value): bool {
                $fieldValue = static::getObjectFieldValue($object, $field);

                return !\in_array($fieldValue, $value, \is_scalar($fieldValue));
            },
            Comparison::CONTAINS => static fn ($object): bool => str_contains((string) static::getObjectFieldValue($object, $field), (string) $value),
            Comparison::MEMBER_OF => static function ($object) use ($field, $value): bool {
                $fieldValues = static::getObjectFieldValue($object, $field);

                if (!\is_array($fieldValues)) {
                    $fieldValues = iterator_to_array($fieldValues);
                }

                return \in_array($value, $fieldValues, true);
            },
            Comparison::STARTS_WITH => static fn ($object): bool => str_starts_with((string) static::getObjectFieldValue($object, $field), (string) $value),
            Comparison::ENDS_WITH => static fn ($object): bool => str_ends_with((string) static::getObjectFieldValue($object, $field), (string) $value),
            default => throw new \RuntimeException('Unknown comparison operator: ' . $comparison->getOperator()),
        };
    }

    /**
     * {@inheritDoc}
     */
    public function walkValue(Value $value)
    {
        return $value->getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function walkCompositeExpression(CompositeExpression $expr)
    {
        $expressionList = [];

        foreach ($expr->getExpressionList() as $child) {
            $expressionList[] = $this->dispatch($child);
        }

        return match ($expr->getType()) {
            CompositeExpression::TYPE_AND => $this->andExpressions($expressionList),
            CompositeExpression::TYPE_OR => $this->orExpressions($expressionList),
            CompositeExpression::TYPE_NOT => $this->notExpression($expressionList),
            default => throw new \RuntimeException('Unknown composite ' . $expr->getType()),
        };
    }

    /** @param callable[] $expressions */
    private function andExpressions(array $expressions): \Closure
    {
        return static function ($object) use ($expressions): bool {
            foreach ($expressions as $expression) {
                if (!$expression($object)) {
                    return false;
                }
            }

            return true;
        };
    }

    /** @param callable[] $expressions */
    private function orExpressions(array $expressions): \Closure
    {
        return static function ($object) use ($expressions): bool {
            foreach ($expressions as $expression) {
                if ($expression($object)) {
                    return true;
                }
            }

            return false;
        };
    }

    /** @param callable[] $expressions */
    private function notExpression(array $expressions): \Closure
    {
        return static fn ($object) => !$expressions[0]($object);
    }
}
