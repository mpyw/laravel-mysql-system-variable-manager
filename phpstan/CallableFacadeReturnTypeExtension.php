<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\PHPStan;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

use function count;
use function in_array;
use function strlen;

final class CallableFacadeReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return DB::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        $methods = [
            'setSystemVariable',
            'setSystemVariables',
            'usingSystemVariable',
            'usingSystemVariables',
        ];

        return in_array($methodReflection->getName(), $methods, true);
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        if ($methodReflection->getName()[0] === 's') {
            return new ObjectType(Connection::class);
        }

        $offset = $methodReflection->getName()[strlen($methodReflection->getName()) - 1] === 's' ? 1 : 2;

        if (count($methodCall->getArgs()) > $offset) {
            $type = $scope->getType($methodCall->getArgs()[$offset]->value);

            if ($type instanceof ParametersAcceptor) {
                return $type->getReturnType();
            }
        }

        return new MixedType();
    }
}
