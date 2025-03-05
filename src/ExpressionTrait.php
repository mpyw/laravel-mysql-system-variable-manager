<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

use PDO;

/**
 * Trait ExpressionTrait
 */
trait ExpressionTrait
{
    /**
     * Return type.
     */
    abstract public function getType(): string;

    /**
     * Return PDO::PARAM_* type.
     */
    public function getParamType(): int
    {
        switch ($this->getType()) {
            case ExpressionInterface::TYPE_INTEGER:
                return PDO::PARAM_INT;

            case ExpressionInterface::TYPE_BOOLEAN:
                return PDO::PARAM_BOOL;

            case ExpressionInterface::TYPE_FLOAT:
            case ExpressionInterface::TYPE_STRING:
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * Return a placeholder format.
     */
    public function getPlaceholder(): string
    {
        switch ($this->getType()) {
            case ExpressionInterface::TYPE_FLOAT:
                return 'cast(? as decimal(65, 30))';

            case ExpressionInterface::TYPE_INTEGER:
            case ExpressionInterface::TYPE_BOOLEAN:
            case ExpressionInterface::TYPE_STRING:
            default:
                return '?';
        }
    }
}
