<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use PDO;

class SystemVariableSelector
{
    /**
     * Select current MySQL system variable values.
     *
     * @param  array                                                    $newValues
     * @return \Mpyw\LaravelMySqlSystemVariableManager\ValueInterface[]
     */
    public static function selectOriginalVariables(PDO $pdo, array $newValues): array
    {
        if (!$newValues) {
            return [];
        }

        $stmt = $pdo->query(SystemVariableGrammar::selectStatement(\array_keys($newValues)));

        if (!$stmt) {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        $original = $stmt->fetch(PDO::FETCH_ASSOC);

        \assert(\is_array($original));

        foreach ($original as $key => $value) {
            $original[$key] = Value::as(Value::wrap($newValues[$key])->getType(), $value);
        }

        return $original;
    }
}
