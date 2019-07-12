<?php

namespace Lampager\Laravel\Tests;

use Mpyw\LaravelMysqlSystemVariableManager\MySqlConnection;
use PDOException;

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
    public function testBasicVariables(string $variableName, bool $emulated, $expectedOriginal, $newValue, $expectedChanged): void
    {
        $this->{$emulated ? 'onEmulatedConnection' : 'onNativeConnection'}(function (MySqlConnection $db) use ($variableName, $expectedOriginal, $newValue, $expectedChanged) {
            $this->assertSame($expectedOriginal, $db->selectOne("select @@{$variableName} as value")->value);
            $db->setSystemVariable($variableName, $newValue);
            $this->assertSame($expectedChanged, $db->selectOne("select @@{$variableName} as value")->value);
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
        ];
    }

    public function testAssigningNullThrowsExceptionOnNative(): void
    {
        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("SQLSTATE[42000]: Syntax error or access violation: 1231 Variable 'foreign_key_checks' can't be set to the value of 'NULL'");

        $this->onNativeConnection(function (MySqlConnection $db) {
            $db->setSystemVariable('foreign_key_checks', null);
            $db->getPdo();
        });
    }

    public function testAssigningNullThrowsExceptionOnEmulation(): void
    {
        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("SQLSTATE[42000]: Syntax error or access violation: 1231 Variable 'foreign_key_checks' can't be set to the value of 'NULL'");

        $this->onEmulatedConnection(function (MySqlConnection $db) {
            $db->setSystemVariable('foreign_key_checks', null);
            $db->getPdo();
        });
    }

    public function testAssigningNullDoesNotThrowOnUnresolvedConnection(): void
    {
        $this->onNativeConnection(function (MySqlConnection $db) {
            $db->setSystemVariable('foreign_key_checks', null);
        });
        $this->onEmulatedConnection(function (MySqlConnection $db) {
            $db->setSystemVariable('foreign_key_checks', null);
        });
        $this->assertTrue(true);
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
