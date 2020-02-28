<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class CallbackIntegerReplacer implements IntegerReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * ClosureIntegerReplacer constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Replace integer variable value.
     *
     * @param  int $value
     * @return int
     */
    public function replace(int $value): int
    {
        return ($this->callback)($value);
    }

    /**
     * Return type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ExpressionInterface::TYPE_INTEGER;
    }
}
