<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionNamedType;

class Value implements ValueInterface
{
    use ExpressionTrait;

    /**
     * @var bool|float|int|string
     */
    protected $value;

    protected string $type;

    /**
     * Create new int value for MySQL system variable.
     *
     * @return static
     */
    public static function int(int $value)
    {
        return new static($value, static::TYPE_INTEGER);
    }

    /**
     * Create new bool value for MySQL system variable.
     *
     * @return static
     */
    public static function bool(bool $value)
    {
        return new static($value, static::TYPE_BOOLEAN);
    }

    /**
     * Create new float value for MySQL system variable.
     *
     * @return static
     */
    public static function float(float $value)
    {
        return new static($value, static::TYPE_FLOAT);
    }

    /**
     * Create new string value for MySQL system variable.
     *
     * @return static
     */
    public static function str(string $value)
    {
        return new static($value, static::TYPE_STRING);
    }

    /**
     * Create new typed value for MySQL system variable.
     *
     * @param bool|float|int|string $value
     */
    public static function as(string $type, $value): ExpressionInterface
    {
        switch ($type) {
            case static::TYPE_INTEGER:
                return static::int((int)$value);
            case static::TYPE_BOOLEAN:
                return static::bool((bool)$value);
            case static::TYPE_FLOAT:
                return static::float((float)$value);
            case static::TYPE_STRING:
                return static::str((string)$value);
            default:
                throw new InvalidArgumentException('The type must be one of "integer", "boolean", "double" or "string".');
        }
    }

    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * Automatically wrap a non-null value.
     *
     * @param mixed $value
     */
    public static function wrap($value): ExpressionInterface
    {
        if ($value instanceof ExpressionInterface) {
            return $value;
        }

        if (\is_scalar($value)) {
            return static::as(gettype($value), $value);
        }

        if ($value instanceof Closure) {
            /* @noinspection PhpUnhandledExceptionInspection */
            $reflector = new ReflectionFunction($value);
            $returnType = $reflector->getReturnType();
            if ($returnType instanceof ReflectionNamedType && !$returnType->allowsNull()) {
                $type = ExpressionInterface::GRAMMATICAL_TYPE_TO_STRING_TYPE[$returnType->getName()] ?? null;
                if ($type) {
                    return Replacer::as($type, $value);
                }
            }
        }

        throw new InvalidArgumentException('The value must be a scalar, return-type-explicit closure or ' . ExpressionInterface::class . ' instance.');
    }

    /**
     * Value constructor.
     *
     * @param bool|float|int|string $value
     */
    final protected function __construct($value, string $type)
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
}
