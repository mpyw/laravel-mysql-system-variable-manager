<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Closure;
use LogicException;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\BooleanReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\FloatReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\IntegerReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\StringReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Value as BindingValue;
use Mpyw\LaravelPdoEmulationControl\EmulationController;
use Mpyw\Unclosure\Value;
use PDO;
use Mpyw\LaravelMySqlSystemVariableManager\SystemVariableGrammar as Grammar;
use PDOStatement;

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
     * @param  \PDO   $pdo
     * @param  string $query
     * @param  array  $values
     * @return \PDO
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
     * @param  \PDO   $pdo
     * @param  string $query
     * @param  array  $values
     * @return \PDO
     */
    protected static function withStatementFor(PDO $pdo, string $query, array $values): PDO
    {
        $expressions = array_map([BindingValue::class, 'wrap'], $values);
        $original = static::selectOriginalVariablesForReplacer($pdo, $expressions);
        $statement = $pdo->prepare($query);

        $i = 0;
        foreach ($expressions as $key => $expression) {
            static::bindValue($statement, ++$i, $expression, $original[$key] ?? null);
        }
        $statement->execute();

        return $pdo;
    }

    /**
     * @param  \PDO                                                          $pdo
     * @param  \Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface[] $expressions
     * @return \Mpyw\LaravelMySqlSystemVariableManager\ValueInterface[]
     */
    protected static function selectOriginalVariablesForReplacer(PDO $pdo, array $expressions): array
    {
        $replacers = array_filter($expressions, function ($value) {
            return $value instanceof IntegerReplacerInterface
                || $value instanceof BooleanReplacerInterface
                || $value instanceof FloatReplacerInterface
                || $value instanceof StringReplacerInterface;
        });

        return SystemVariableSelector::selectOriginalVariables($pdo, $replacers);
    }

    /**
     * @param \PDOStatement                                               $statement
     * @param int                                                         $parameter
     * @param \Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface $expression
     * @param null|\Mpyw\LaravelMySqlSystemVariableManager\ValueInterface $original
     */
    protected static function bindValue(PDOStatement $statement, int $parameter, ExpressionInterface $expression, ?ValueInterface $original = null): void
    {
        if ($expression instanceof ValueInterface) {
            $statement->bindValue($parameter, $expression->getValue(), $expression->getParamType());
            return;
        }

        if (($expression instanceof IntegerReplacerInterface
            || $expression instanceof BooleanReplacerInterface
            || $expression instanceof FloatReplacerInterface
            || $expression instanceof StringReplacerInterface
            ) && $original) {
            $statement->bindValue($parameter, $expression->replace($original->getValue()), $expression->getParamType());
            return;
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Unreachable code.');
        // @codeCoverageIgnoreEnd
    }
}
