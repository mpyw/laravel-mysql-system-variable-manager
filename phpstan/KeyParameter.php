<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\PHPStan;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

final class KeyParameter implements ParameterReflection
{
    public function getName(): string
    {
        return 'key';
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getType(): Type
    {
        return new StringType();
    }

    public function passedByReference(): PassedByReference
    {
        return PassedByReference::createNo();
    }

    public function isVariadic(): bool
    {
        return false;
    }

    public function getDefaultValue(): ?Type
    {
        return null;
    }
}
