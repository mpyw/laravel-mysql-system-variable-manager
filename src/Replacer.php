<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use InvalidArgumentException;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\BooleanReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\CallbackBooleanReplacer;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\CallbackFloatReplacer;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\CallbackIntegerReplacer;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\CallbackStringReplacer;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\FloatReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\IntegerReplacerInterface;
use Mpyw\LaravelMySqlSystemVariableManager\Replacers\StringReplacerInterface;

class Replacer
{
    /**
     * Create new int replacer for MySQL system variable.
     *
     * @phpstan-param callable(int): int $callback
     */
    public static function int(callable $callback): IntegerReplacerInterface
    {
        return new CallbackIntegerReplacer($callback);
    }

    /**
     * Create new bool replacer for MySQL system variable.
     *
     * @phpstan-param callable(bool): bool $callback
     */
    public static function bool(callable $callback): BooleanReplacerInterface
    {
        return new CallbackBooleanReplacer($callback);
    }

    /**
     * Create new float replacer for MySQL system variable.
     *
     * @phpstan-param callable(float): float $callback
     */
    public static function float(callable $callback): FloatReplacerInterface
    {
        return new CallbackFloatReplacer($callback);
    }

    /**
     * Create new string replacer for MySQL system variable.
     *
     * @phpstan-param callable(string): string $callback
     */
    public static function str(callable $callback): StringReplacerInterface
    {
        return new CallbackStringReplacer($callback);
    }

    /**
     * Create new typed replacer for MySQL system variable.
     */
    public static function as(string $type, callable $callback): ExpressionInterface
    {
        switch ($type) {
            case ExpressionInterface::TYPE_INTEGER:
                return static::int($callback);
            case ExpressionInterface::TYPE_BOOLEAN:
                return static::bool($callback);
            case ExpressionInterface::TYPE_FLOAT:
                return static::float($callback);
            case ExpressionInterface::TYPE_STRING:
                return static::str($callback);
            default:
                throw new InvalidArgumentException('The return type must be one of "integer", "boolean", "double" or "string".');
        }
    }
}
