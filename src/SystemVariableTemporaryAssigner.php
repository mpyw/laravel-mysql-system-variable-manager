<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Mpyw\Unclosure\Value;
use PDO;

class SystemVariableTemporaryAssigner
{
    /**
     * @var \Closure[]|\PDO[]
     */
    protected $pdos;

    /**
     * SystemVariableAssigner constructor.
     *
     * @param null|\Closure|\PDO &...$pdos
     */
    public function __construct(&...$pdos)
    {
        $this->pdos = array_filter($pdos);
    }

    /**
     * Temporarily set MySQL system variables for PDO.
     *
     * @param  array    $using
     * @param  callable $callback
     * @param  array    $args
     * @return $this
     */
    public function using(array $using, callable $callback, ...$args)
    {
        return Value::withEffectForEach($this->pdos, function (PDO $pdo) use ($using) {
            $original = SystemVariableSelector::selectOriginalVariables($pdo, $using);
            (new SystemVariableAssigner($pdo))->assign($using);

            return function (PDO $pdo) use ($original) {
                (new SystemVariableAssigner($pdo))->assign($original);
            };
        }, $callback, ...$args);
    }
}
