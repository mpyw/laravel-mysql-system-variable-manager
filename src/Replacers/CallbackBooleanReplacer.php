<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class CallbackBooleanReplacer implements BooleanReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var callable
     * @phpstan-var callable(bool): bool
     */
    protected $callback;

    /**
     * ClosureBooleanReplacer constructor.
     *
     * @phpstan-param callable(bool): bool $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Replace boolean variable value.
     */
    public function replace(bool $value): bool
    {
        return ($this->callback)($value);
    }

    /**
     * Return type.
     *
     * @phpstan-return ExpressionInterface::TYPE_BOOLEAN
     */
    public function getType(): string
    {
        return ExpressionInterface::TYPE_BOOLEAN;
    }
}
