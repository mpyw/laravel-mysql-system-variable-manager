<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

interface ValueInterface
{
    public const TYPE_INT = 'integer';
    public const TYPE_BOOL = 'boolean';
    public const TYPE_FLOAT = 'double';
    public const TYPE_STR = 'string';

    /**
     * Return original value.
     *
     * @return bool|float|int|string
     */
    public function getValue();

    /**
     * Return type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Return PDO::PARAM_* type.
     *
     * @return int
     */
    public function getParamType(): int;

    /**
     * Return placeholder for prepared statement.
     *
     * @return string
     */
    public function getPlaceholder(): string;
}
