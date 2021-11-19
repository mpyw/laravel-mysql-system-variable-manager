<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

class SystemVariableMemoizedAssigner
{
    protected SystemVariableAwareReconnector $reconnector;

    /**
     * @var \Closure[]|\PDO[]
     */
    protected array $pdos;

    /**
     * SystemVariableMemoizedAssigner constructor.
     *
     * @param null|callable|\Mpyw\LaravelMySqlSystemVariableManager\SystemVariableAwareReconnector &$reconnector
     * @param null|\Closure|\PDO &...$pdos
     */
    public function __construct(&$reconnector, &...$pdos)
    {
        $this->reconnector = $reconnector = !$reconnector instanceof SystemVariableAwareReconnector
            ? new SystemVariableAwareReconnector($reconnector)
            : $reconnector;

        $this->pdos = \array_filter($pdos);
    }

    /**
     * Set MySQL system variables for PDO.
     *
     * @param  array $values
     * @return $this
     */
    public function assign(array $values, bool $memoizeForReconnect = true)
    {
        (new SystemVariableAssigner(...$this->pdos))
            ->assign($values);

        if ($memoizeForReconnect) {
            $this->reconnector->memoizeSystemVariables($values);
        }

        return $this;
    }
}
