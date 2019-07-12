<?php

namespace Mpyw\LaravelMysqlSystemVariableManager;

use Illuminate\Database\ConnectionInterface;
use LogicException;

/**
 * Class SystemVariableAwareReconnector
 */
class SystemVariableAwareReconnector
{
    /**
     * @var array
     */
    protected $memoizedSystemVariables = [];

    /**
     * @var null|callable
     */
    protected $reconnector;

    /**
     * SystemVariableAwareReconnector constructor.
     *
     * @param null|callable $reconnector
     */
    public function __construct(?callable $reconnector = null)
    {
        $this->reconnector = $reconnector;
    }

    /**
     * @param  array $values
     * @return $this
     */
    public function memoizeSystemVariables(array $values)
    {
        $this->memoizedSystemVariables = array_replace($this->memoizedSystemVariables, $values);
        return $this;
    }

    /**
     * @param  \Illuminate\Database\ConnectionInterface|\Mpyw\LaravelMysqlSystemVariableManager\ManagesSystemVariables $connection
     * @return mixed
     */
    public function __invoke(ConnectionInterface $connection)
    {
        if (is_callable($this->reconnector)) {
            $result = ($this->reconnector)($connection);
            $connection->setSystemVariables($this->memoizedSystemVariables, true);
            return $result;
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Lost connection and no reconnector available.');
        // @codeCoverageIgnoreEnd
    }
}
