<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class CallbackStringReplacer implements StringReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var callable
     * @phpstan-var callable(string): string
     */
    protected $callback;

    /**
     * ClosureStringReplacer constructor.
     *
     * @phpstan-param callable(string): string $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Replace string variable value.
     */
    public function replace(string $value): string
    {
        return ($this->callback)($value);
    }

    /**
     * Return type.
     *
     * @phpstan-return ExpressionInterface::TYPE_STRING
     */
    public function getType(): string
    {
        return ExpressionInterface::TYPE_STRING;
    }
}
