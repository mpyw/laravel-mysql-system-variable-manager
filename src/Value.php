<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use InvalidArgumentException;
use PDO;

class Value implements ValueInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $type;

    /**
     * Create new int value for MySQL system variable.
     *
     * @param  int    $value
     * @return static
     */
    public static function int(int $value)
    {
        return new static($value, static::TYPE_INT);
    }

    /**
     * Create new bool value for MySQL system variable.
     *
     * @param  bool   $value
     * @return static
     */
    public static function bool(bool $value)
    {
        return new static($value, static::TYPE_BOOL);
    }

    /**
     * Create new float value for MySQL system variable.
     *
     * @param  float  $value
     * @return static
     */
    public static function float(float $value)
    {
        return new static($value, static::TYPE_FLOAT);
    }

    /**
     * Create new string value for MySQL system variable.
     *
     * @param  string $value
     * @return static
     */
    public static function str(string $value)
    {
        return new static($value, static::TYPE_STR);
    }

    /**
     * Create new typed value for MySQL system variable.
     *
     * @param  string                                                 $type
     * @param  bool|float|int|string                                  $value
     * @return \Mpyw\LaravelMySqlSystemVariableManager\ValueInterface
     */
    public static function as(string $type, $value): ValueInterface
    {
        switch ($type) {
            case static::TYPE_INT:
                return static::int($value);
            case static::TYPE_BOOL:
                return static::bool($value);
            case static::TYPE_FLOAT:
                return static::float($value);
            case static::TYPE_STR:
                return static::str($value);
            default:
                throw new InvalidArgumentException('The type must be one of "integer", "boolean", "double" or "string".');
        }
    }

    /**
     * Automatically wrap a non-null value.
     *
     * @param  mixed                                                  $value
     * @return \Mpyw\LaravelMySqlSystemVariableManager\ValueInterface
     */
    public static function wrap($value): ValueInterface
    {
        if ($value instanceof ValueInterface) {
            return $value;
        }
        if (is_scalar($value)) {
            return static::as(gettype($value), $value);
        }
        throw new InvalidArgumentException('The value must be a scalar or ' . ValueInterface::class . ' instance.');
    }

    /**
     * Value constructor.
     *
     * @param mixed  $value
     * @param string $type
     */
    protected function __construct($value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * Return original value.
     *
     * @return bool|float|int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return PDO::PARAM_* type.
     *
     * @return int
     */
    public function getParamType(): int
    {
        switch ($this->type) {
            case static::TYPE_INT:
                return PDO::PARAM_INT;
            case static::TYPE_BOOL:
                return PDO::PARAM_BOOL;
            case static::TYPE_FLOAT:
            case static::TYPE_STR:
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * Return a placeholder format.
     *
     * @return string
     */
    public function getPlaceholder(): string
    {
        switch ($this->type) {
            case static::TYPE_FLOAT:
                return 'cast(? as decimal(65, 30))';
            case static::TYPE_INT:
            case static::TYPE_BOOL:
            case static::TYPE_STR:
            default:
                return '?';
        }
    }
}
