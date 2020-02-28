<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class CallbackStringReplacer implements StringReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * ClosureStringReplacer constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Replace string variable value.
     *
     * @param  string $value
     * @return string
     */
    public function replace(string $value): string
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
        return ExpressionInterface::TYPE_STRING;
    }
}
