<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class CallbackFloatReplacer implements FloatReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * ClosureFloatReplacer constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Replace float variable value.
     *
     * @param  float $value
     * @return float
     */
    public function replace(float $value): float
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
        return ExpressionInterface::TYPE_FLOAT;
    }
}
