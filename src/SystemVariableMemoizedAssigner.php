<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Closure;
use PDO;

use function array_filter;

class SystemVariableMemoizedAssigner
{
    protected SystemVariableAwareReconnector $reconnector;

    /**
     * @var Closure[]|PDO[]
     */
    protected array $pdos;

    /**
     * SystemVariableMemoizedAssigner constructor.
     *
     * @param null|callable|SystemVariableAwareReconnector &$reconnector
     * @param null|Closure|PDO &...$pdos
     */
    /**
     * @phpstan-ignore-next-line parameterByRef.unusedType
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
     * @param  array<string, mixed> $values
     * @return $this
     */
    public function assign(array $values, bool $memoizeForReconnect = true)
    {
        // @phpstan-ignore-next-line assign.propertyType
        (new SystemVariableAssigner(...$this->pdos))
            ->assign($values);

        if ($memoizeForReconnect) {
            $this->reconnector->memoizeSystemVariables($values);
        }

        return $this;
    }
}
