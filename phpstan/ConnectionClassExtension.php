<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\PHPStan;

use Illuminate\Database\ConnectionInterface;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;

use function in_array;

final class ConnectionClassExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        $methods = [
            'setSystemVariable',
            'setSystemVariables',
            'usingSystemVariable',
            'usingSystemVariables',
        ];

        return in_array($methodName, $methods, true)
            && $classReflection->is(ConnectionInterface::class);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return new SystemVariablesMethod($classReflection, $methodName);
    }
}
