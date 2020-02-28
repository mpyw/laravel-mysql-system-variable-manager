<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;

interface StringReplacerInterface extends ExpressionInterface
{
    /**
     * Replace string variable value.
     *
     * @param  string $value
     * @return string
     */
    public function replace(string $value): string;
}
