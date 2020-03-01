<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Tests;

use Mpyw\LaravelMySqlSystemVariableManager\MySqlConnection;
use Mpyw\LaravelMySqlSystemVariableManager\SqlMode;

class SQLModeTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.connections.mysql.strict', false);
        $app['config']->set('database.connections.mysql:emulated.strict', false);
    }

    /**
     * @return array
     */
    public function modeProvider(): array
    {
        return [[true], [false]];
    }

    /**
     * @param bool $emulated
     * @dataProvider modeProvider
     */
    public function testSetSqlMode(bool $emulated): void
    {
        $this->{$emulated ? 'onEmulatedConnection' : 'onNativeConnection'}(function (MySqlConnection $db) {
            $this->assertSame('NO_ENGINE_SUBSTITUTION', $db->selectOne('select @@sql_mode as value')->value);

            $db->setSqlMode(
                SqlMode::enable('ONLY_FULL_GROUP_BY')
                    ->enable('STRICT_TRANS_TABLES')
                    ->disable('NO_ENGINE_SUBSTITUTION')
            );
            $this->assertSame('ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES', $db->selectOne('select @@sql_mode as value')->value);

            $db->setSqlMode(
                SqlMode::disable('ONLY_FULL_GROUP_BY')
                    ->enable('ERROR_FOR_DIVISION_BY_ZERO', 'NO_ZERO_DATE')
                    ->disable('ERROR_FOR_DIVISION_BY_ZERO')
            );
            $this->assertSame('STRICT_TRANS_TABLES,NO_ZERO_DATE', $db->selectOne('select @@sql_mode as value')->value);

            $db->setSqlMode(
                SqlMode::set('NO_AUTO_CREATE_USER')
                    ->enable('NO_ENGINE_SUBSTITUTION')
            );
            $this->assertSame('NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION', $db->selectOne('select @@sql_mode as value')->value);
        });
    }

    /**
     * @param bool $emulated
     * @dataProvider modeProvider
     */
    public function testUsingSqlMode(bool $emulated): void
    {
        $this->{$emulated ? 'onEmulatedConnection' : 'onNativeConnection'}(function (MySqlConnection $db) {
            $this->assertSame('NO_ENGINE_SUBSTITUTION', $db->selectOne('select @@sql_mode as value')->value);

            $db->usingSqlMode(
                SqlMode::enable('ONLY_FULL_GROUP_BY')
                    ->enable('STRICT_TRANS_TABLES')
                    ->disable('NO_ENGINE_SUBSTITUTION'),
                function () use ($db) {
                    $this->assertSame('ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES', $db->selectOne('select @@sql_mode as value')->value);
                }
            );
            $this->assertSame('NO_ENGINE_SUBSTITUTION', $db->selectOne('select @@sql_mode as value')->value);

            $db->usingSqlMode(
                SqlMode::enable('ONLY_FULL_GROUP_BY')
                    ->set(''),
                function () use ($db) {
                    $this->assertSame('', $db->selectOne('select @@sql_mode as value')->value);
                }
            );
            $this->assertSame('NO_ENGINE_SUBSTITUTION', $db->selectOne('select @@sql_mode as value')->value);

            $db->usingSqlMode(
                SqlMode::enable('ONLY_FULL_GROUP_BY')
                    ->enable('STRICT_TRANS_TABLES')
                    ->set()
                    ->enable('NO_ZERO_DATE')
                    ->disable('NO_ZERO_DATE')
                    ->enable('STRICT_TRANS_TABLES'),
                function () use ($db) {
                    $this->assertSame('STRICT_TRANS_TABLES', $db->selectOne('select @@sql_mode as value')->value);
                }
            );
            $this->assertSame('NO_ENGINE_SUBSTITUTION', $db->selectOne('select @@sql_mode as value')->value);
        });
    }
}
