<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\PHPStan;

use Illuminate\Database\ConnectionInterface;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;

final class CallableReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ConnectionInterface::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $methods = [
            'setSystemVariable',
            'setSystemVariables',
            'usingSystemVariable',
            'usingSystemVariables',
        ];

        return \in_array($methodReflection->getName(), $methods, true);
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        if ($methodReflection->getName()[0] === 's') {
            return new ThisType($methodReflection->getDeclaringClass());
        }

        $offset = $methodReflection->getName()[\strlen($methodReflection->getName()) - 1] === 's' ? 1 : 2;

        if (\count($methodCall->getArgs()) > $offset) {
            $type = $scope->getType($methodCall->getArgs()[$offset]->value);

            if ($type instanceof ParametersAcceptor) {
                return $type->getReturnType();
            }
        }

        return new MixedType();
    }
}
