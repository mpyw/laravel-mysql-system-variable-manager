<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

class SystemVariableMemoizedAssigner
{
    /**
     * @var \Mpyw\LaravelMySqlSystemVariableManager\SystemVariableAwareReconnector
     */
    protected $reconnector;

    /**
     * @var \Closure[]|\PDO[]
     */
    protected $pdos;

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

        $this->pdos = array_filter($pdos);
    }

    /**
     * Set MySQL system variables for PDO.
     *
     * @param  array $values
     * @param  bool  $memoizeForReconnect
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
