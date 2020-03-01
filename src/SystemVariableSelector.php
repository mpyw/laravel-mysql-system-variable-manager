<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use PDO;

class SystemVariableSelector
{
    /**
     * Select current MySQL system variable values.
     *
     * @param  \PDO                                                     $pdo
     * @param  array                                                    $newValues
     * @return \Mpyw\LaravelMySqlSystemVariableManager\ValueInterface[]
     */
    public static function selectOriginalVariables(PDO $pdo, array $newValues): array
    {
        if (!$newValues) {
            return [];
        }

        $original = $pdo
            ->query(SystemVariableGrammar::selectStatement(array_keys($newValues)))
            ->fetch(PDO::FETCH_ASSOC);

        foreach ($original as $key => $value) {
            $original[$key] = Value::as(Value::wrap($newValues[$key])->getType(), $value);
        }

        return $original;
    }
}
