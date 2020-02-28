<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class CallbackBooleanReplacer implements BooleanReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * ClosureBooleanReplacer constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Replace boolean variable value.
     *
     * @param  bool $value
     * @return bool
     */
    public function replace(bool $value): bool
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
        return ExpressionInterface::TYPE_BOOLEAN;
    }
}
