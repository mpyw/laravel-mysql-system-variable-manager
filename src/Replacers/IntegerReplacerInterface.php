<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;

interface IntegerReplacerInterface extends ExpressionInterface
{
    /**
     * Replace integer variable value.
     *
     * @param  int $value
     * @return int
     */
    public function replace(int $value): int;
}
