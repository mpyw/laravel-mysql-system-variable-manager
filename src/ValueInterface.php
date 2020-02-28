<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

interface ValueInterface extends ExpressionInterface
{
    /**
     * Return original value.
     *
     * @return bool|float|int|string
     */
    public function getValue();
}
