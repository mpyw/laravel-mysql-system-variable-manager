<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Mpyw\LaravelMySqlSystemVariableManager\Value as BindingValue;
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
            $original = $this->selectOriginalVariables($pdo, $using);
            (new SystemVariableAssigner($pdo))->assign($using);

            return function (PDO $pdo) use ($original) {
                (new SystemVariableAssigner($pdo))->assign($original);
            };
        }, $callback, ...$args);
    }

    /**
     * Select current MySQL system variable values.
     *
     * @param  \PDO                                            $pdo
     * @param  array                                           $using
     * @return \Mpyw\LaravelMySqlSystemVariableManager\Value[]
     */
    protected function selectOriginalVariables(PDO $pdo, array $using): array
    {
        if (!$using) {
            return [];
        }

        $original = $pdo
            ->query(SystemVariableGrammar::selectStatement(array_keys($using)))
            ->fetch(PDO::FETCH_ASSOC);

        foreach ($original as $key => $value) {
            $original[$key] = BindingValue::as(BindingValue::wrap($using[$key])->getType(), $value);
        }

        return $original;
    }
}
