<?php

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
     * @param  array  $values
     * @param  bool   $memoizeForReconnect
     * @return $this
     */
    public function setSystemVariables(array $values, bool $memoizeForReconnect = true)
    {
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
     * @param  array $values
     * @param  mixed ...$args
     * @return mixed
     */
    public function usingSystemVariables(array $values, callable $callback, ...$args)
    {
        return (new SystemVariableTemporaryAssigner($this->readPdo, $this->pdo))
            ->using($values, $callback, ...$args);
    }
}
