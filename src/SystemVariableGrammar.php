<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

class SystemVariableGrammar
{
    /**
     * @param  array  $values
     * @return string
     */
    public static function assignmentStatement(array $values): string
    {
        return 'set session ' . implode(', ', static::assignmentExpressions($values));
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

    /**
     * @param  string $identifier
     * @return string
     */
    public static function escapeIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
