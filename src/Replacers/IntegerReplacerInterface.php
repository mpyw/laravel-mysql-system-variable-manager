<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;

interface IntegerReplacerInterface extends ExpressionInterface
{
    /**
     * Replace integer variable value.
     */
    public function replace(int $value): int;
}
