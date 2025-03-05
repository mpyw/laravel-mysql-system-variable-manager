<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Illuminate\Database\ConnectionInterface;
use LogicException;

use function array_replace;
use function is_callable;
use function method_exists;

/**
 * Class SystemVariableAwareReconnector
 */
class SystemVariableAwareReconnector
{
    /**
     * @var array<string, mixed>
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
     * @param  array<string, mixed> $values
     * @return $this
     */
    public function memoizeSystemVariables(array $values)
    {
        $this->memoizedSystemVariables = array_replace($this->memoizedSystemVariables, $values);

        return $this;
    }

    /**
     * @return mixed
     */
    public function __invoke(ConnectionInterface $connection)
    {
        // @phpstan-ignore-next-line function.alreadyNarrowedType
        if (is_callable($this->reconnector) && method_exists($connection, 'setSystemVariables')) {
            $result = ($this->reconnector)($connection);
            $connection->setSystemVariables($this->memoizedSystemVariables, true);

            return $result;
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Lost connection and no reconnector available.');
        // @codeCoverageIgnoreEnd
    }
}
