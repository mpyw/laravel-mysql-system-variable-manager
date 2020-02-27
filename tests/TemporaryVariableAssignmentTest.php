<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Tests;

use Mpyw\LaravelMySqlSystemVariableManager\MySqlConnection;
use Mpyw\LaravelMySqlSystemVariableManager\Value;

class TemporaryVariableAssignmentTest extends TestCase
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
    public function testBasicVariables(string $variableName, bool $emulated, $expectedOriginal, $newValue, $expectedChanged): void
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
        ];
    }
}
