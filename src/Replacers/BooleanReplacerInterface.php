<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;

interface BooleanReplacerInterface extends ExpressionInterface
{
    /**
     * Replace boolean variable value.
     */
    public function replace(bool $value): bool;
}
