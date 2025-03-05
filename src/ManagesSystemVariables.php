<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

/**
 * Trait ManagesSystemVariables
 *
 * @mixin \Illuminate\Database\MySqlConnection
 */
trait ManagesSystemVariables
{
    /**
     * Set MySQL system variable for both read and write PDOs.
     * It is lazily executed for unresolved PDO instance.
     *
     * @param  mixed $value
     * @return $this
     */
    public function setSystemVariable(string $key, $value, bool $memoizeForReconnect = true)
    {
        return $this->setSystemVariables([$key => $value], $memoizeForReconnect);
    }

    /**
     * Set MySQL system variables for both read and write PDOs.
     * It is lazily executed for unresolved PDO instance.
     *
     * @param  array<string, mixed> $values
     * @return $this
     */
    public function setSystemVariables(array $values, bool $memoizeForReconnect = true)
    {
        // @phpstan-ignore-next-line assign.propertyType
        (new SystemVariableMemoizedAssigner($this->reconnector, $this->readPdo, $this->pdo))
            ->assign($values, $memoizeForReconnect);

        return $this;
    }

    /**
     * Run callback temporarily setting MySQL system variable for both read and write PDOs.
     * It is lazily executed for unresolved PDO instance.
     *
     * @param  mixed $value
     * @param  mixed ...$args
     * @return mixed
     */
    public function usingSystemVariable(string $key, $value, callable $callback, ...$args)
    {
        return $this->usingSystemVariables([$key => $value], $callback, ...$args);
    }

    /**
     * Run callback temporarily setting MySQL system variables for both read and write PDOs.
     * It is lazily executed for unresolved PDO instance.
     *
     * @param  array<string, mixed> $values
     * @return mixed
     */
    public function usingSystemVariables(array $values, callable $callback, mixed ...$args)
    {
        // @phpstan-ignore-next-line assign.propertyType
        return (new SystemVariableTemporaryAssigner($this->readPdo, $this->pdo))
            ->using($values, $callback, ...$args);
    }
}
