<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;

interface StringReplacerInterface extends ExpressionInterface
{
    /**
     * Replace string variable value.
     */
    public function replace(string $value): string;
}
