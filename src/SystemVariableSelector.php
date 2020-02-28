<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Mpyw\LaravelMySqlSystemVariableManager\Value as BindingValue;
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
            $original[$key] = BindingValue::as(BindingValue::wrap($newValues[$key])->getType(), $value);
        }

        return $original;
    }
}
