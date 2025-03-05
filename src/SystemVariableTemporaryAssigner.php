<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Mpyw\Unclosure\Value as ValueEffector;
use PDO;
use Closure;

use function array_filter;

class SystemVariableTemporaryAssigner
{
    /**
     * @var Closure[]|PDO[]
     */
    protected array $pdos;

    /**
     * SystemVariableAssigner constructor.
     *
     * @param null|Closure|PDO &...$pdos
     */
    /**
     * @phpstan-ignore-next-line parameterByRef.unusedType
     */
    public function __construct(&...$pdos)
    {
        $this->pdos = array_filter($pdos);
    }

    /**
     * Temporarily set MySQL system variables for PDO.
     *
     * @param  array<string, mixed> $using
     * @param  mixed                ...$args
     * @return $this
     */
    public function using(array $using, callable $callback, ...$args)
    {
        // @phpstan-ignore-next-line: assign.propertyType
        return ValueEffector::withEffectForEach($this->pdos, static function (PDO $pdo) use ($using) {
            $original = SystemVariableSelector::selectOriginalVariables($pdo, $using);
            (new SystemVariableAssigner($pdo))->assign($using);

            return static function (PDO $pdo) use ($original): void {
                (new SystemVariableAssigner($pdo))->assign($original);
            };
        }, $callback, ...$args);
    }
}
