<?php

namespace Mpyw\LaravelMysqlSystemVariableManager;

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
        foreach (array_filter([&$this->pdo, &$this->readPdo]) as &$pdo) {
            $pdo = PdoDecorator::withSystemVariables($pdo, $values);
        }

        if (!$this->reconnector instanceof SystemVariableAwareReconnector) {
            $this->setReconnector(new SystemVariableAwareReconnector($this->reconnector));
        }
        if ($memoizeForReconnect) {
            $this->reconnector->memoizeSystemVariables($values);
        }

        return $this;
    }
}
