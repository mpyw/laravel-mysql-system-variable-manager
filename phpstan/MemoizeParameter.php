<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\PHPStan;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Type;

final class MemoizeParameter implements ParameterReflection
{
    public function getName(): string
    {
        return 'memoizeForReconnect';
    }

    public function isOptional(): bool
    {
        return true;
    }

    public function getType(): Type
    {
        return new BooleanType();
    }

    public function passedByReference(): PassedByReference
    {
        return PassedByReference::createNo();
    }

    public function isVariadic(): bool
    {
        return false;
    }

    public function getDefaultValue(): Type
    {
        return new ConstantBooleanType(true);
    }
}
