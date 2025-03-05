<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class CallbackFloatReplacer implements FloatReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var callable
     * @phpstan-var callable(float): float
     */
    protected $callback;

    /**
     * ClosureFloatReplacer constructor.
     *
     * @phpstan-param callable(float): float $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Replace float variable value.
     */
    public function replace(float $value): float
    {
        return ($this->callback)($value);
    }

    /**
     * Return type.
     *
     * @phpstan-return ExpressionInterface::TYPE_FLOAT
     */
    public function getType(): string
    {
        return ExpressionInterface::TYPE_FLOAT;
    }
}
