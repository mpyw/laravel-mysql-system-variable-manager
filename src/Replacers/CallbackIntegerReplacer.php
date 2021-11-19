<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class CallbackIntegerReplacer implements IntegerReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var callable
     * @phpstan-var callable(int): int
     */
    protected $callback;

    /**
     * ClosureIntegerReplacer constructor.
     *
     * @phpstan-param callable(int): int $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Replace integer variable value.
     */
    public function replace(int $value): int
    {
        return ($this->callback)($value);
    }

    /**
     * Return type.
     *
     * @phpstan-return ExpressionInterface::TYPE_INTEGER
     */
    public function getType(): string
    {
        return ExpressionInterface::TYPE_INTEGER;
    }
}
