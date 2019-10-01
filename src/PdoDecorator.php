<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Closure;
use PDO;

/**
 * Class PdoDecorator
 */
class PdoDecorator
{
    /**
     * Set MySQL system variables for PDO.
     *
     * @param  \Closure|\PDO $pdo
     * @param  array         $values
     * @return \Closure|\PDO
     */
    public static function withSystemVariables($pdo, array $values)
    {
        return $values
            ? static::withEmulatedStatement(
                $pdo,
                'set session ' . implode(', ', static::buildExpressions($values)),
                $values
            )
            : $pdo;
    }

    /**
     * Configure PDO using query and parameters temporarily enabling PDO::ATTR_EMULATE_PREPARES.
     *
     * @param  \Closure|\PDO $pdo
     * @param  string        $query
     * @param  array         $values
     * @return \Closure|\PDO
     */
    public static function withEmulatedStatement($pdo, string $query, array $values = [])
    {
        return static::withCallback($pdo, function (PDO $pdo) use ($query, $values) {
            $enabled = $pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            try {
                return static::withStatement($pdo, $query, $values);
            } finally {
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $enabled);
            }
        });
    }

    /**
     * Configure PDO using query and parameters.
     *
     * @param  \Closure|\PDO $pdo
     * @param  string        $query
     * @param  array         $values
     * @return \Closure|\PDO
     */
    public static function withStatement($pdo, string $query, array $values = [])
    {
        return static::withCallback($pdo, function (PDO $pdo) use ($query, $values) {
            $statement = $pdo->prepare($query);
            foreach (array_values($values) as $i => $value) {
                $statement->bindValue($i + 1, $value, static::getParamTypeForValue($value));
            }
            $statement->execute();
            return $pdo;
        });
    }

    /**
     * Configure PDO via callback.
     *
     * @param  \Closure|\PDO $pdo
     * @param  callable      $callback
     * @return \Closure|\PDO
     */
    public static function withCallback($pdo, callable $callback)
    {
        return $pdo instanceof Closure
            ? function () use ($pdo, $callback) {
                return $callback($pdo());
            }
        : $callback($pdo);
    }

    /**
     * @param  array $values
     * @return array
     */
    protected static function buildExpressions(array $values): array
    {
        $expressions = [];
        foreach ($values as $name => $value) {
            $expressions[] = static::escapeIdentifier($name) . '=' . static::getPlaceHolderForValue($value);
        }
        return $expressions;
    }

    /**
     * @param  mixed $value
     * @return int
     */
    protected static function getParamTypeForValue($value): int
    {
        switch (gettype($value)) {
            case 'NULL':
                return PDO::PARAM_NULL;
            case 'integer':
                return PDO::PARAM_INT;
            case 'boolean':
                return PDO::PARAM_BOOL;
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * @param  mixed  $value
     * @return string
     */
    protected static function getPlaceHolderForValue($value): string
    {
        switch (gettype($value)) {
            case 'double':
                return 'CAST(? AS DECIMAL(65, 30))';
            default:
                return '?';
        }
    }

    /**
     * @param  string $identifier
     * @return string
     */
    protected static function escapeIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
