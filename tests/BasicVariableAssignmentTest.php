<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Tests;

use InvalidArgumentException;
use Mpyw\LaravelMySqlSystemVariableManager\MySqlConnection;
use Mpyw\LaravelMySqlSystemVariableManager\Replacer;
use Mpyw\LaravelMySqlSystemVariableManager\Value;

class BasicVariableAssignmentTest extends TestCase
{
    /**
     * @test
     * @param string $variableName
     * @param bool   $emulated
     * @param mixed  $expectedOriginal
     * @param mixed  $newValue
     * @param mixed  $expectedChanged
     * @dataProvider provideBasicVariables
     */
    public function testAssignments(string $variableName, bool $emulated, $expectedOriginal, $newValue, $expectedChanged): void
    {
        $this->{$emulated ? 'onEmulatedConnection' : 'onNativeConnection'}(function (MySqlConnection $db) use ($variableName, $expectedOriginal, $newValue, $expectedChanged) {
            $this->assertSame($expectedOriginal, $db->selectOne("select @@{$variableName} as value")->value);
            $db->setSystemVariable($variableName, $newValue);
            $this->assertSame($expectedChanged, $db->selectOne("select @@{$variableName} as value")->value);
        });
    }

    /**
     * @test
     * @param string $variableName
     * @param bool   $emulated
     * @param mixed  $expectedOriginal
     * @param mixed  $newValue
     * @param mixed  $expectedChanged
     * @dataProvider provideBasicVariables
     */
    public function testTemporaryAssignments(string $variableName, bool $emulated, $expectedOriginal, $newValue, $expectedChanged): void
    {
        $this->{$emulated ? 'onEmulatedConnection' : 'onNativeConnection'}(function (MySqlConnection $db) use ($variableName, $expectedOriginal, $newValue, $expectedChanged) {
            $this->assertSame($expectedOriginal, $db->selectOne("select @@{$variableName} as value")->value);
            $db->usingSystemVariable($variableName, $newValue, function () use ($expectedChanged, $db, $variableName) {
                $this->assertSame($expectedChanged, $db->selectOne("select @@{$variableName} as value")->value);
            });
            $this->assertSame($expectedOriginal, $db->selectOne("select @@{$variableName} as value")->value);
        });
    }

    /**
     * @return array
     */
    public function provideBasicVariables(): array
    {
        return [
            'assigning float (native)' => ['long_query_time', false, 10.0, 15.0, 15.0],
            'assigning float (emulated)' => ['long_query_time', true, '10.000000', 15.0, '15.000000'],
            'assigning integer (native)' => ['long_query_time', false, 10.0, 15, 15.0],
            'assigning integer (emulated)' => ['long_query_time', true, '10.000000', 15, '15.000000'],
            'assigning boolean (native)' => ['foreign_key_checks', false, 1, false, 0],
            'assigning boolean (emulated)' => ['foreign_key_checks', true, '1', false, '0'],
            'assigning string (native)' => ['tx_isolation', false, 'REPEATABLE-READ', 'read-committed', 'READ-COMMITTED'],
            'assigning string (emulated)' => ['tx_isolation', true, 'REPEATABLE-READ', 'read-committed', 'READ-COMMITTED'],
            'assigning wrapped float (native)' => ['long_query_time', false, 10.0, Value::float(15.0), 15.0],
            'assigning wrapped float (emulated)' => ['long_query_time', true, '10.000000', Value::float(15.0), '15.000000'],
            'assigning wrapped integer (native)' => ['long_query_time', false, 10.0, Value::int(15), 15.0],
            'assigning wrapped integer (emulated)' => ['long_query_time', true, '10.000000', Value::int(15), '15.000000'],
            'assigning wrapped boolean (native)' => ['foreign_key_checks', false, 1, Value::bool(false), 0],
            'assigning wrapped boolean (emulated)' => ['foreign_key_checks', true, '1', Value::bool(false), '0'],
            'assigning wrapped string (native)' => ['tx_isolation', false, 'REPEATABLE-READ', Value::str('read-committed'), 'READ-COMMITTED'],
            'assigning wrapped string (emulated)' => ['tx_isolation', true, 'REPEATABLE-READ', Value::str('read-committed'), 'READ-COMMITTED'],
            'replacing explicit float (native)' => ['long_query_time', false, 10.0, Replacer::float(function ($v) { return $v + 5.0; }), 15.0],
            'replacing explicit float (emulated)' => ['long_query_time', true, '10.000000', Replacer::float(function ($v) { return $v + 5.0; }), '15.000000'],
            'replacing explicit integer (native)' => ['long_query_time', false, 10.0, Replacer::int(function ($v) { return $v + 5; }), 15.0],
            'replacing explicit integer (emulated)' => ['long_query_time', true, '10.000000', Replacer::int(function ($v) { return $v + 5; }), '15.000000'],
            'replacing explicit boolean (native)' => ['foreign_key_checks', false, 1, Replacer::bool(function ($v) { return !$v; }), 0],
            'replacing explicit boolean (emulated)' => ['foreign_key_checks', true, '1', Replacer::bool(function ($v) { return !$v; }), '0'],
            'replacing explicit string (native)' => ['tx_isolation', false, 'REPEATABLE-READ', Replacer::str(function ($v) { return str_ireplace('repeatable-read', 'read-committed', $v); }), 'READ-COMMITTED'],
            'replacing explicit string (emulated)' => ['tx_isolation', true, 'REPEATABLE-READ', Replacer::str(function ($v) { return str_ireplace('repeatable-read', 'read-committed', $v); }), 'READ-COMMITTED'],
            'replacing implicit float (native)' => ['long_query_time', false, 10.0, function ($v): float { return $v + 5.0; }, 15.0],
            'replacing implicit float (emulated)' => ['long_query_time', true, '10.000000', function ($v): float { return $v + 5.0; }, '15.000000'],
            'replacing implicit integer (native)' => ['long_query_time', false, 10.0, function ($v): int { return $v + 5; }, 15.0],
            'replacing implicit integer (emulated)' => ['long_query_time', true, '10.000000', function ($v): int { return $v + 5; }, '15.000000'],
            'replacing implicit boolean (native)' => ['foreign_key_checks', false, 1, function ($v): bool { return !$v; }, 0],
            'replacing implicit boolean (emulated)' => ['foreign_key_checks', true, '1', function ($v): bool { return !$v; }, '0'],
            'replacing implicit string (native)' => ['tx_isolation', false, 'REPEATABLE-READ', function ($v): string { return str_ireplace('repeatable-read', 'read-committed', $v); }, 'READ-COMMITTED'],
            'replacing implicit string (emulated)' => ['tx_isolation', true, 'REPEATABLE-READ', function ($v): string { return str_ireplace('repeatable-read', 'read-committed', $v); }, 'READ-COMMITTED'],
        ];
    }

    public function testAssigningNullThrowsExceptionOnNative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be a scalar, return-type-explicit closure or Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface instance.');

        $this->onNativeConnection(function (MySqlConnection $db) {
            $db->setSystemVariable('foreign_key_checks', null);
            $db->getPdo();
        });
    }

    public function testAssigningNullThrowsExceptionOnEmulation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be a scalar, return-type-explicit closure or Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface instance.');

        $this->onEmulatedConnection(function (MySqlConnection $db) {
            $db->setSystemVariable('foreign_key_checks', null);
            $db->getPdo();
        });
    }

    public function testAssigningNullThrowsOnUnresolvedNativeConnection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be a scalar, return-type-explicit closure or Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface instance.');

        $this->onNativeConnection(function (MySqlConnection $db) {
            $db->setSystemVariable('foreign_key_checks', null);
        });
    }

    public function testAssigningNullThrowsOnUnresolvedEmulatedConnection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be a scalar, return-type-explicit closure or Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface instance.');

        $this->onEmulatedConnection(function (MySqlConnection $db) {
            $db->setSystemVariable('foreign_key_checks', null);
        });
    }

    public function testAssignmentPriorityOnLazilyResolvedConnection(): void
    {
        $this->onNativeConnection(function (MySqlConnection $db) {
            $db
                ->setSystemVariable('long_query_time', 11.0)
                ->setSystemVariable('long_query_time', 12.0)
                ->setSystemVariable('long_query_time', 13.0);
            $db->getPdo();
            $this->assertSame(13.0, $db->selectOne('select @@long_query_time as value')->value);
        });

        $this->onEmulatedConnection(function (MySqlConnection $db) {
            $db
                ->setSystemVariable('long_query_time', 11.0)
                ->setSystemVariable('long_query_time', 12.0)
                ->setSystemVariable('long_query_time', 13.0);
            $db->getPdo();
            $this->assertSame('13.000000', $db->selectOne('select @@long_query_time as value')->value);
        });
    }

    public function testAssignmentPriorityOnEagerlyResolvedConnection(): void
    {
        $this->onNativeConnection(function (MySqlConnection $db) {
            $db->getPdo();
            $db
                ->setSystemVariable('long_query_time', 11.0)
                ->setSystemVariable('long_query_time', 12.0)
                ->setSystemVariable('long_query_time', 13.0);
            $this->assertSame(13.0, $db->selectOne('select @@long_query_time as value')->value);
        });

        $this->onEmulatedConnection(function (MySqlConnection $db) {
            $db->getPdo();
            $db
                ->setSystemVariable('long_query_time', 11.0)
                ->setSystemVariable('long_query_time', 12.0)
                ->setSystemVariable('long_query_time', 13.0);
            $this->assertSame('13.000000', $db->selectOne('select @@long_query_time as value')->value);
        });
    }
}
