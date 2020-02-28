<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;

interface FloatReplacerInterface extends ExpressionInterface
{
    /**
     * Replace float variable value.
     *
     * @param  float $value
     * @return float
     */
    public function replace(float $value): float;
}
