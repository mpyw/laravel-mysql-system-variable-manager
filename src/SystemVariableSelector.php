<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

use PDO;

use function array_keys;
use function assert;
use function is_array;

class SystemVariableSelector
{
    /**
     * Select current MySQL system variable values.
     *
     * @param  array<string, mixed> $newValues
     * @return ValueInterface[]
     */
    public static function selectOriginalVariables(PDO $pdo, array $newValues): array
    {
        if (!$newValues) {
            return [];
        }

        $stmt = $pdo->query(SystemVariableGrammar::selectStatement(array_keys($newValues)));

        if (!$stmt) {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        $original = $stmt->fetch(PDO::FETCH_ASSOC);

        assert(is_array($original));

        foreach ($original as $key => $value) {
            // @phpstan-ignore-next-line
            $original[$key] = Value::as(Value::wrap($newValues[$key])->getType(), $value);
        }

        return $original;
    }
}
