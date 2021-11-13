<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Tests;

use Illuminate\Support\Facades\DB;
use Mpyw\LaravelMySqlSystemVariableManager\MySqlConnection;

class ReconnectionTest extends TestCase
{
    public function testOnlyMemoizedVariablesAreReassigned(): void
    {
        $this->onNativeConnection(function (MySqlConnection $db) {
            $this->assertSame('REPEATABLE-READ', $db->selectOne('select @@tx_isolation as value')->value);
            $this->assertSame(10.0, $db->selectOne('select @@long_query_time as value')->value);

            $db
                ->setSystemVariable('tx_isolation', 'read-committed')
                ->setSystemVariable('long_query_time', 13.0, false);
            $db->getPdo();
            $db->reconnect();

            $this->assertSame('READ-COMMITTED', $db->selectOne('select @@tx_isolation as value')->value);
            $this->assertSame(10.0, $db->selectOne('select @@long_query_time as value')->value);
        });

        $this->onEmulatedConnection(function (MySqlConnection $db) {
            $this->assertSame('REPEATABLE-READ', $db->selectOne('select @@tx_isolation as value')->value);
            $this->assertSame($this->v81('10.000000', 10.0), $db->selectOne('select @@long_query_time as value')->value);

            $db
                ->setSystemVariable('tx_isolation', 'read-committed')
                ->setSystemVariable('long_query_time', 13.0, false);
            $db->getPdo();
            $db->reconnect();

            $this->assertSame('READ-COMMITTED', $db->selectOne('select @@tx_isolation as value')->value);
            $this->assertSame($this->v81('10.000000', 10.0), $db->selectOne('select @@long_query_time as value')->value);
        });
    }

    public function testDirectReconnectionCallDoesNotWork(): void
    {
        $this->assertSame('REPEATABLE-READ', DB::selectOne('select @@tx_isolation as value')->value);
        $this->assertSame(10.0, DB::selectOne('select @@long_query_time as value')->value);

        DB::setSystemVariable('tx_isolation', 'read-committed');
        DB::setSystemVariable('long_query_time', 13.0, false);
        DB::getPdo();

        // This operation does not work.
        // Please use DB::connection()->reconnect() instead.
        DB::reconnect();

        $this->assertSame('REPEATABLE-READ', DB::selectOne('select @@tx_isolation as value')->value);
        $this->assertSame(10.0, DB::selectOne('select @@long_query_time as value')->value);
    }
}
