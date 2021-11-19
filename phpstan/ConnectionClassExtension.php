<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\PHPStan;

use Illuminate\Database\ConnectionInterface;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;

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

        return \in_array($methodName, $methods, true)
            && \is_a($classReflection->getName(), ConnectionInterface::class, true);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return new SystemVariablesMethod($classReflection, $methodName);
    }
}
