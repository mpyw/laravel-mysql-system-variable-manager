<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\PHPStan;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

final class ValuesParameter implements ParameterReflection
{
    public function getName(): string
    {
        return 'values';
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getType(): Type
    {
        return new ArrayType(
            new StringType(),
            new MixedType(),
        );
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
