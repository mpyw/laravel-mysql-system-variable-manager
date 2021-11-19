<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

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
    protected array $memoizedSystemVariables = [];

    /**
     * @var null|callable
     */
    protected $reconnector;

    /**
     * SystemVariableAwareReconnector constructor.
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
        $this->memoizedSystemVariables = \array_replace($this->memoizedSystemVariables, $values);
        return $this;
    }

    /**
     * @return mixed
     */
    public function __invoke(ConnectionInterface $connection)
    {
        if (\is_callable($this->reconnector) && \method_exists($connection, 'setSystemVariables')) {
            $result = ($this->reconnector)($connection);
            $connection->setSystemVariables($this->memoizedSystemVariables, true);
            return $result;
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Lost connection and no reconnector available.');
        // @codeCoverageIgnoreEnd
    }
}
