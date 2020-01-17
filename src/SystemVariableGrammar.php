<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use PDO;

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
            $expressions[] = static::escapeIdentifier($name) . '=' . static::placeholderFor($value);
        }
        return $expressions;
    }

    /**
     * @param  mixed $value
     * @return int
     */
    public static function paramTypeFor($value): int
    {
        switch (gettype($value)) {
            case 'integer':
                return PDO::PARAM_INT;
            case 'boolean':
                return PDO::PARAM_BOOL;
            case 'NULL':
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * @param  mixed  $value
     * @return string
     */
    public static function placeholderFor($value): string
    {
        switch (gettype($value)) {
            case 'double':
                return 'cast(? as decimal(65, 30))';
            default:
                return '?';
        }
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
