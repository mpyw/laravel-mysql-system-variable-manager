<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

interface ExpressionInterface
{
    public const TYPE_INTEGER = 'integer';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_FLOAT = 'double';
    public const TYPE_STRING = 'string';

    public const GRAMMATICAL_TYPE_TO_STRING_TYPE = [
        'int' => self::TYPE_INTEGER,
        'bool' => self::TYPE_BOOLEAN,
        'float' => self::TYPE_FLOAT,
        'double' => self::TYPE_FLOAT,
        'string' => self::TYPE_STRING,
    ];

    /**
     * Return type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Return PDO::PARAM_* type.s
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
