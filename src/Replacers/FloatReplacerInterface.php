<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;

interface FloatReplacerInterface extends ExpressionInterface
{
    /**
     * Replace float variable value.
     */
    public function replace(float $value): float;
}
