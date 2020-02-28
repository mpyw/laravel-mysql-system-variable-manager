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
     * Set the reconnect instance on the connection.
     *
     * @param  callable $reconnector
     * @return $this
     */
    abstract public function setReconnector(callable $reconnector);

    /**
     * Set MySQL system variable for both read and write PDOs.
     * It is lazily executed for unresolved PDO instance.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  bool   $memoizeForReconnect
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
     * @param  array $values
     * @param  bool  $memoizeForReconnect
     * @return $this
     */
    public function setSystemVariables(array $values, bool $memoizeForReconnect = true)
    {
        (new SystemVariableAssigner($this->readPdo, $this->pdo))->assign($values);

        if (!$this->reconnector instanceof SystemVariableAwareReconnector) {
            $this->setReconnector(new SystemVariableAwareReconnector($this->reconnector));
        }
        if ($memoizeForReconnect) {
            $this->reconnector->memoizeSystemVariables($values);
        }

        return $this;
    }

    /**
     * Run callback temporarily setting MySQL system variable for both read and write PDOs.
     * It is lazily executed for unresolved PDO instance.
     *
     * @param  string   $key
     * @param  mixed    $value
     * @param  callable $callback
     * @param  mixed    ...$args
     * @return $this
     */
    public function usingSystemVariable(string $key, $value, callable $callback, ...$args)
    {
        return $this->usingSystemVariables([$key => $value], $callback, ...$args);
    }

    /**
     * Run callback temporarily setting MySQL system variables for both read and write PDOs.
     * It is lazily executed for unresolved PDO instance.
     *
     * @param  array    $values
     * @param  callable $callback
     * @param  mixed    ...$args
     * @return $this
     */
    public function usingSystemVariables(array $values, callable $callback, ...$args)
    {
        (new SystemVariableTemporaryAssigner($this->readPdo, $this->pdo))
            ->using($values, $callback, ...$args);

        return $this;
    }
}
