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
     * @phpstan-return self::TYPE_INTEGER|self::TYPE_BOOLEAN|self::TYPE_FLOAT|self::TYPE_STRING
     */
    public function getType(): string;

    /**
     * Return PDO::PARAM_* type.
     */
    public function getParamType(): int;

    /**
     * Return placeholder for prepared statement.
     */
    public function getPlaceholder(): string;
}
