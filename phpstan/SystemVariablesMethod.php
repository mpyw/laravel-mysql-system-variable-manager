<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\PHPStan;

use Illuminate\Database\QueryException;
use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;

use function strlen;

final class SystemVariablesMethod implements MethodReflection
{
    private ClassReflection $class;
    private string $name;

    public function __construct(ClassReflection $classReflection, string $methodName)
    {
        $this->class = $classReflection;
        $this->name = $methodName;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->class;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getDocComment(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this;
    }

    /**
     * @return list<FunctionVariant>
     */
    public function getVariants(): array
    {
        return $this->getName()[0] === 's'
            ? $this->getSetVariants()
            : $this->getUsingVariants();
    }

    /**
     * @return list<FunctionVariant>
     */
    private function getSetVariants(): array
    {
        return [new FunctionVariant(
            TemplateTypeMap::createEmpty(),
            null,
            $this->getName()[strlen($this->getName()) - 1] === 's'
                ? [
                    new ValuesParameter(),
                    new MemoizeParameter(),
                ]
                : [
                    new KeyParameter(),
                    new ValueParameter(),
                    new MemoizeParameter(),
                ],
            false,
            new ThisType($this->class),
        )];
    }

    /**
     * @return list<FunctionVariant>
     */
    private function getUsingVariants(): array
    {
        $baseArgs = $this->getName()[strlen($this->getName()) - 1] === 's'
            ? [
                new ValuesParameter(),
            ]
            : [
                new KeyParameter(),
                new ValueParameter(),
            ];

        $variants = [];

        for ($i = 0; $i < 10; ++$i) {
            $argumentParameters = [];
            for ($j = 0; $j < $i; ++$j) {
                $argumentParameters[] = new CallableArgumentParameter();
            }

            $variants[] = new FunctionVariant(
                TemplateTypeMap::createEmpty(),
                null,
                [
                    ...$baseArgs,
                    new CallableParameter($argumentParameters),
                    ...$argumentParameters,
                ],
                false,
                new MixedType(),
            );
        }

        return $variants;
    }

    public function isDeprecated(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function getDeprecatedDescription(): ?string
    {
        return null;
    }

    public function isFinal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function isInternal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function getThrowType(): Type
    {
        return new ObjectType(QueryException::class);
    }

    public function hasSideEffects(): TrinaryLogic
    {
        return TrinaryLogic::createMaybe();
    }
}
