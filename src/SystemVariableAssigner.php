<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Closure;
use Mpyw\LaravelMySqlSystemVariableManager\Value as BindingValue;
use Mpyw\LaravelPdoEmulationControl\EmulationController;
use Mpyw\Unclosure\Value;
use PDO;
use Mpyw\LaravelMySqlSystemVariableManager\SystemVariableGrammar as Grammar;

class SystemVariableAssigner
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
     * Set MySQL system variables for PDO.
     *
     * @param  array $values
     * @return $this
     */
    public function assign(array $values)
    {
        return $values
            ? $this->withEmulatedStatement(Grammar::assignmentStatement($values), $values)
            : $this;
    }

    /**
     * Configure PDO using query and parameters temporarily enabling PDO::ATTR_EMULATE_PREPARES.
     *
     * @param  string $query
     * @param  array  $values
     * @return $this
     */
    protected function withEmulatedStatement(string $query, array $values = [])
    {
        foreach ($this->pdos as &$pdo) {
            $pdo = Value::withCallback(
                $pdo,
                Closure::fromCallable([$this, 'withEmulatedStatementFor']),
                $query,
                $values
            );
        }
        unset($pdo);

        return $this;
    }

    /**
     * @param  PDO    $pdo
     * @param  string $query
     * @param  array  $values
     * @return PDO
     */
    protected static function withEmulatedStatementFor(PDO $pdo, string $query, array $values): PDO
    {
        return (new EmulationController($pdo))->emulated(
            Closure::fromCallable([static::class, 'withStatementFor']),
            $pdo,
            $query,
            $values
        );
    }

    /**
     * @param  PDO    $pdo
     * @param  string $query
     * @param  array  $values
     * @return PDO
     */
    protected static function withStatementFor(PDO $pdo, string $query, array $values): PDO
    {
        $statement = $pdo->prepare($query);
        foreach (array_values($values) as $i => $value) {
            $value = BindingValue::wrap($value);
            $statement->bindValue($i + 1, $value->getValue(), $value->getParamType());
        }
        $statement->execute();

        return $pdo;
    }
}
