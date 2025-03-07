<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Closure;
use LogicException;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\BooleanReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\FloatReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\IntegerReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\StringReplacerInterface;
use Mpyw\LaravelPdoEmulationControl\EmulationController;
use Mpyw\Unclosure\Value as ValueEffector;
use PDO;
use Mpyw\LaravelMySqlSystemVariableManager\SystemVariableGrammar as Grammar;
use PDOStatement;

use function array_filter;
use function array_map;

class SystemVariableAssigner
{
    /**
     * @var Closure[]|PDO[]
     */
    protected array $pdos;

    /**
     * SystemVariableAssigner constructor.
     */
    /**
     * @phpstan-ignore-next-line parameterByRef.unusedType
     */
    public function __construct(null|Closure|PDO &...$pdos)
    {
        $this->pdos = array_filter($pdos);
    }

    /**
     * Set MySQL system variables for PDO.
     *
     * @param  array<string, mixed> $values
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
     * @param  array<string, mixed> $values
     * @return $this
     */
    /**
     * @phpstan-ignore-next-line missingType.iterableValue
     */
    protected function withEmulatedStatement(string $query, array $values = [])
    {
        foreach ($this->pdos as &$pdo) {
            $pdo = ValueEffector::withCallback(
                $pdo,
                Closure::fromCallable([$this, 'withEmulatedStatementFor']),
                $query,
                $values,
            );
        }
        unset($pdo);

        return $this;
    }

    /**
     * @param array<string, mixed> $values
     */
    /**
     * @phpstan-ignore-next-line missingType.iterableValue
     */
    protected static function withEmulatedStatementFor(PDO $pdo, string $query, array $values): PDO
    {
        return (new EmulationController($pdo))->emulated(
            Closure::fromCallable([static::class, 'withStatementFor']),
            $pdo,
            $query,
            $values,
        );
    }

    /**
     * @param array<string, mixed> $values
     */
    /**
     * @phpstan-ignore-next-line missingType.iterableValue
     */
    protected static function withStatementFor(PDO $pdo, string $query, array $values): PDO
    {
        $expressions = array_map([Value::class, 'wrap'], $values);
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
     * @param  ExpressionInterface[] $expressions
     * @return ValueInterface[]
     */
    protected static function selectOriginalVariablesForReplacer(PDO $pdo, array $expressions): array
    {
        $replacers = array_filter($expressions, static function ($value) {
            return $value instanceof IntegerReplacerInterface
                || $value instanceof BooleanReplacerInterface
                || $value instanceof FloatReplacerInterface
                || $value instanceof StringReplacerInterface;
        });

        return SystemVariableSelector::selectOriginalVariables($pdo, $replacers);
    }

    protected static function bindValue(PDOStatement $statement, int $parameter, ExpressionInterface $expression, ?ValueInterface $original = null): void
    {
        if ($expression instanceof ValueInterface) {
            $statement->bindValue($parameter, $expression->getValue(), $expression->getParamType());

            return;
        }

        if ($original) {
            if ($expression instanceof IntegerReplacerInterface) {
                $statement->bindValue($parameter, $expression->replace((int)$original->getValue()), $expression->getParamType());

                return;
            }
            if ($expression instanceof BooleanReplacerInterface) {
                $statement->bindValue($parameter, $expression->replace((bool)$original->getValue()), $expression->getParamType());

                return;
            }
            if ($expression instanceof FloatReplacerInterface) {
                $statement->bindValue($parameter, $expression->replace((float)$original->getValue()), $expression->getParamType());

                return;
            }
            if ($expression instanceof StringReplacerInterface) {
                $statement->bindValue($parameter, $expression->replace((string)$original->getValue()), $expression->getParamType());

                return;
            }
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Unreachable code.');
        // @codeCoverageIgnoreEnd
    }
}
