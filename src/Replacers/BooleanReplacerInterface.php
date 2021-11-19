<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;

interface BooleanReplacerInterface extends ExpressionInterface
{
    /**
     * Replace boolean variable value.
     */
    public function replace(bool $value): bool;
}
