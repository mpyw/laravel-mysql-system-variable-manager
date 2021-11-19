<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

class SystemVariableGrammar
{
    /**
     * @param string[] $variables
     */
    public static function selectStatement(array $variables): string
    {
        return 'select ' . \implode(', ', static::variableExpressions($variables));
    }

    /**
     * @param string[] $variables
     * @return string[]
     */
    public static function variableExpressions(array $variables): array
    {
        $expressions = [];
        foreach ($variables as $variable) {
            $expressions[] = \sprintf('@@%1$s as %1$s', static::escapeIdentifier($variable));
        }
        return $expressions;
    }

    /**
     * @param array $values
     */
    public static function assignmentStatement(array $values): string
    {
        return 'set session ' . \implode(', ', static::assignmentExpressions($values));
    }

    /**
     * @param  array    $values
     * @return string[]
     */
    public static function assignmentExpressions(array $values): array
    {
        $expressions = [];
        foreach ($values as $name => $value) {
            $expressions[] = static::escapeIdentifier($name) . '=' . Value::wrap($value)->getPlaceholder();
        }
        return $expressions;
    }

    public static function escapeIdentifier(string $identifier): string
    {
        return '`' . \str_replace('`', '``', $identifier) . '`';
    }
}
